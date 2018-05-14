<?php

namespace Laralabs\Toaster\Tests;

use Illuminate\Support\Facades\Config;
use Laralabs\Toaster\Toaster;
use Laralabs\Toaster\ToasterGroup;
use Session;

class ToasterTest extends TestCase
{
    /** @test */
    public function toaster_function_returns_toaster_instance()
    {
        $toaster = toaster();

        $this->assertInstanceOf(Toaster::class, $toaster);
    }

    /** @test */
    public function it_displays_default_toast_and_can_clear_all()
    {
        $this->toaster->add('cheese');

        $this->assertCount(1, $this->toaster->groups);

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertCount(1, $group->messages);

        $toast = $group->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('info', $toast->type);
        $this->assertEquals('', $toast->title);
        $this->assertEquals($this->lifetime, $toast->duration);
        $this->assertEquals($this->animationSpeed, $toast->speed);

        $this->assertSessionHas('toaster', [
            'data' => [
                'default' => [
                    'name' => 'default',
                    'width' => $this->width,
                    'classes' => [],
                    'animation_type' => $this->animationType,
                    'animation_name' => null,
                    'position' => $this->position,
                    'max' => $this->limit,
                    'reverse' => $this->reverse,
                    'messages' => $this->toaster->groups->first()->messages->toArray(),
                    'velocity_config' => 'velocity'
                ]
            ],
        ]);

        $this->toaster->clear();

        $this->assertSessionHas('toaster', [
            'data' => []
        ]);
    }

    /** @test */
    public function it_can_create_a_toaster_group_and_set_name()
    {
        $this->toaster->group('toastie');

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_width()
    {
        $this->toaster->group('toastie')->width('100%');

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals('100%', $group->properties['width']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_classes()
    {
        $this->toaster->group('toastie')->classes(['salt', 'pepper']);

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals(['salt', 'pepper'], $group->properties['classes']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_animation_type_and_velocity_config()
    {
        $this->toaster->group('toastie')->animationType('velocity')->velocityConfig('velocity');

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals('velocity', $group->properties['animation_type']);
        $this->assertEquals('velocity', $group->properties['velocity_config']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_animation_name()
    {
        $this->toaster->group('toastie')->animationName('animation-name');

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals('animation-name', $group->properties['animation_name']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_position()
    {
        $this->toaster->group('toastie')->position('top left');

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals('top left', $group->properties['position']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_max_toasts()
    {
        $this->toaster->group('toastie')->max(10);

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals(10, $group->properties['max']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_set_group_reverse_order()
    {
        $this->toaster->group('toastie')->reverse(true);

        $group = $this->toaster->groups->first();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals(true, $group->properties['reverse']);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_mass_update_last_group_properties()
    {
        $properties = [
            'name' => 'toastie',
            'width' => '500px',
            'classes' => ['salt', 'pepper'],
            'animation_type' => 'css',
            'animation_name' => 'animation-name',
            'velocity_config' => 'velocity',
            'position' => 'bottom left',
            'max' => 15,
            'reverse' => true
        ];

        $this->toaster->group($properties['name'])
            ->properties($properties);

        $group = $this->toaster->groups->last();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);
        $this->assertEquals($properties, $group->properties);

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_mass_update_last_toast_properties()
    {
        $properties = [
            'group' => 'toastie',
            'message' => 'cheese',
            'type' => 'warn',
            'title' => 'Toastie Ingredients',
            'duration' => $this->lifetime,
            'speed' => $this->animationSpeed
        ];

        $this->toaster->group($properties['group'])
            ->add('cheese')
            ->update($properties);

        $group = $this->toaster->groups->last();
        $toast = $group->messages->last();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);

        foreach ($properties as $property => $value) {
            $this->assertEquals($value, $toast->$property);
        }

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_add_toast_with_properties()
    {
        $properties = [
            'group' => 'toastie',
            'message' => 'cheese',
            'type' => 'warn',
            'title' => 'Toastie Ingredients',
            'duration' => $this->lifetime,
            'speed' => $this->animationSpeed
        ];

        $this->toaster->group($properties['group'])
            ->add('cheese', null, $properties);

        $group = $this->toaster->groups->last();
        $toast = $group->messages->last();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);

        foreach ($properties as $property => $value) {
            $this->assertEquals($value, $toast->$property);
        }

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_add_toast_to_specified_group()
    {
        $properties = [
            'group' => 'toastie',
            'message' => 'cheese',
            'type' => 'warn',
            'title' => 'Toastie Ingredients',
            'duration' => $this->lifetime + $this->interval,
            'speed' => $this->animationSpeed
        ];

        $this->toaster->group('toastie')->add('ham')
            ->group('toastie-two')
            ->add('cheese')
            ->add('cheese', null, $properties, $properties['group']);

        $group = $this->toaster->groups->first();
        $toast = $group->messages->last();

        $this->assertInstanceOf(ToasterGroup::class, $group);
        $this->assertEquals('toastie', $group->name);

        foreach ($properties as $property => $value) {
            $this->assertEquals($value, $toast->$property);
        }

        $this->toaster->clear();
    }

    /** @test */
    public function it_can_stagger_groups()
    {
        Config::set('toaster.toast_stagger_all', false);

        $this->toaster->group('toastie')
            ->add('ham')
            ->add('cheese')
            ->group('toastie-two')
            ->add('cheese')
            ->add('tomato');

        foreach ($this->toaster->groups->all() as $group) {
            $current = $this->lifetime - $this->interval;
            foreach ($group->messages->all() as $message) {
                $current = $current + $this->interval;
                $this->assertEquals($current, $message->duration);
            }
        }
    }

    /** @test */
    public function it_can_stagger_all_groups()
    {
        Config::set('toaster.toast_stagger_all', true);

        $this->toaster->group('toastie')
            ->add('ham')
            ->add('cheese')
            ->group('toastie-two')
            ->add('cheese')
            ->add('tomato');

        $current = $this->lifetime - $this->interval;
        foreach ($this->toaster->groups->all() as $group) {
            $current = config('toaster.toast_stagger_all') ? $current : $this->lifetime - $this->interval;
            foreach ($group->messages->all() as $message) {
                $current = $current + $this->interval;
                $this->assertEquals($current, $message->duration);
            }
        }
    }

    /** @test */
    public function it_displays_multiple_toast()
    {
        $this->toaster->group('toastie')
            ->add('ham')
            ->add('cheese');

        $this->assertCount(2, $this->toaster->groups->first()->messages);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_default_duration_and_speed()
    {
        $this->toaster->group('toastie')->add('cheese');

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals($this->lifetime, $toast->duration);
        $this->assertEquals($this->animationSpeed, $toast->speed);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_custom_duration_and_speed()
    {
        $this->toaster->group('toastie')->add('cheese')->duration(5000)->speed(800);

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals(5000, $toast->duration);
        $this->assertEquals(800, $toast->speed);

        $this->toaster->clear();
    }

    /**
    public function it_sets_multiple_null_expires_with_interval_and_retains_custom_values()
    {
        $this->toaster->add('Beans on Toast');
        $this->toaster->add('Egg on Toast')->expires(9000);
        $this->toaster->add('Cheese on Toast');

        $this->assertCount(3, $this->toaster->messages);
        $expires = $this->lifetime;
        $counter = 1;
        foreach ($this->toaster->messages as $toast) {
            if ($counter == 2) {
                $this->assertEquals(9000, $toast->expires);
            } else {
                $this->assertEquals($expires, $toast->expires);
            }
            $expires = $expires + $this->interval;
            $counter++;
        }
    }

    public function it_generates_correctly_structured_json()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->add('Egg on Toast');

        $this->assertCount(2, $this->toaster->messages);

        $validJson = 'window.toaster = window.toaster || {};toaster.data = {"lifetime":2000,"maxToasts":10,"messages":[{"message":"Beans on Toast","theme":"success","closeBtn":false,"title":"","expires":2000},{"message":"Egg on Toast","theme":"info","closeBtn":false,"title":"","expires":2500}],"position":"top right"}';
        $this->assertEquals($validJson, $this->binder->generateJs());
    }

    public function it_displays_info_toast()
    {
        $this->toaster->add('Info on Toast')->info();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Info on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(2000, $toast->expires);
    }

    public function it_displays_success_toast()
    {
        $this->toaster->add('Success on Toast')->success();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Success on Toast', $toast->message);
        $this->assertEquals('success', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(2000, $toast->expires);
    }

    public function it_displays_error_toast()
    {
        $this->toaster->add('Error on Toast')->error();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Error on Toast', $toast->message);
        $this->assertEquals('error', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(2000, $toast->expires);
    }

    public function it_displays_warning_toast()
    {
        $this->toaster->add('Warning on Toast')->warning();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Warning on Toast', $toast->message);
        $this->assertEquals('warning', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(2000, $toast->expires);
    }

    public function it_sets_close_button()
    {
        $this->toaster->add('Important Information on Toast')->important();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Important Information on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(true, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(2000, $toast->expires);
    }

    public function it_sets_toast_title()
    {
        $this->toaster->add('Information on Toast')->title('Toast is good for you');

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Information on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('Toast is good for you', $toast->title);
        $this->assertEquals(2000, $toast->expires);
    }

    public function it_can_update_last_message_with_update_function()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->update(['theme' => 'error', 'closeBtn' => true, 'title' => 'It now has a title', 'expires' => 5000]);

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Beans on Toast', $toast->message);
        $this->assertEquals('error', $toast->theme);
        $this->assertEquals(true, $toast->closeBtn);
        $this->assertEquals('It now has a title', $toast->title);
        $this->assertEquals(5000, $toast->expires);
    }

    public function it_clears_message_collection()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->add('Egg on Toast');

        $this->assertCount(2, $this->toaster->messages);

        $this->toaster->clear();

        $this->assertCount(0, $this->toaster->messages);
    }

    public function it_can_flash_data_to_session_store()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->add('Egg on Toast');

        $data = [
            'data' => [
                'lifetime'  => $this->lifetime,
                'maxToasts' => $this->limit,
                'messages'  => $this->toaster->messages->toArray(),
                'position'  => $this->position,
            ],
        ];

        $this->session->flash('toaster', $data);

        $this->assertSessionHas('toaster', $data);
    }

    public function it_aborts_expires_non_integer()
    {
        $this->expectExceptionMessage('Argument passed to expires() must be a valid integer (milliseconds)');

        $this->toaster->add('Beans on Toast')->expires('five minutes');
    }
     */

    /** @test */
    public function it_has_mandatory_message_argument()
    {
        if (version_compare(PHP_VERSION, '7.1', '>=')) {
            $this->expectException('ArgumentCountError');
        } else {
            $this->expectException('ErrorException');
        }

        $this->toaster->add();
    }

    /** @test */
    public function it_aborts_editing_non_message()
    {
        $this->expectExceptionMessage('Use the add() function to add a message before attempting to modify it');

        $this->toaster->group('toastie')->success();
    }

    protected function assertSessionHas($name, $value = null)
    {
        $this->assertTrue(Session::has($name), "Session doesn't contain '$name'");
        if ($value) {
            $this->assertEquals($value, Session::get($name), "Session '$name' are not equal to".print_r($value).'');
        }
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
