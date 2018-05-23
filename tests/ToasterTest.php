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
            ->add('cheese', null, $properties);

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
    public function it_throws_exception_for_invalid_group()
    {
        $this->expectExceptionMessage('No group found with the specified name');

        $properties = [
            'group' => 'toastie-invalid-group',
            'message' => 'cheese',
            'type' => 'warn',
            'title' => 'Toastie Ingredients',
            'duration' => $this->lifetime + $this->interval,
            'speed' => $this->animationSpeed
        ];

        $this->toaster->group('toastie')->add('ham')
            ->group('toastie-two')
            ->add('cheese')
            ->add('cheese', null, $properties);

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
                $message->customDuration ? null : $this->assertEquals($current, $message->duration);
                $current = $message->customDuration ? $current - $this->interval : $current;
            }
        }
    }

    /** @test */
    public function it_can_stagger_all_groups_and_retain_custom_duration()
    {
        Config::set('toaster.toast_stagger_all', true);

        $this->toaster->group('toastie')
            ->add('ham')
            ->add('cheese')
            ->group('toastie-two')
            ->add('cheese')->duration(10000)
            ->add('tomato');

        $current = $this->lifetime - $this->interval;
        foreach ($this->toaster->groups->all() as $group) {
            $current = config('toaster.toast_stagger_all') ? $current : $this->lifetime - $this->interval;
            foreach ($group->messages->all() as $message) {
                $current = $current + $this->interval;
                $message->customDuration ? $this->assertEquals(10000, $message->duration) : $this->assertEquals($current, $message->duration);
                $current = $message->customDuration ? $current - $this->interval : $current;
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
        $this->assertTrue($toast->customDuration);
        $this->assertEquals(5000, $toast->duration);
        $this->assertEquals(800, $toast->speed);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_info_toast()
    {
        $this->toaster->group('toastie')->add('cheese')->info();

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('info', $toast->type);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_success_toast()
    {
        $this->toaster->group('toastie')->add('cheese')->success();

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('success', $toast->type);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_warning_toast()
    {
        $this->toaster->group('toastie')->add('cheese')->warning();

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('warn', $toast->type);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_error_toast()
    {
        $this->toaster->group('toastie')->add('cheese')->error();

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('error', $toast->type);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_important_toast()
    {
        $this->toaster->group('toastie')->add('cheese')->important();

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('info', $toast->type);
        $this->assertEquals(-1, $toast->duration);

        $this->toaster->clear();
    }

    /** @test */
    public function it_sets_toast_title()
    {
        $this->toaster->group('toastie')->add('cheese')->title('Toastie Ingredients');

        $this->assertCount(1, $this->toaster->groups->first()->messages);

        $toast = $this->toaster->groups->first()->messages->first();

        $this->assertEquals('cheese', $toast->message);
        $this->assertEquals('info', $toast->type);
        $this->assertEquals('Toastie Ingredients', $toast->title);

        $this->toaster->clear();
    }

    /** @test */
    public function it_generates_correctly_structured_json()
    {
        $this->toaster->group('toastie')->add('ham')->success()->add('cheese');

        $validJson = 'window.toaster = window.toaster || {};toaster.data = {"toastie":{"name":"toastie","width":"300px","classes":[],"animation_type":"velocity","animation_name":null,"velocity_config":"velocity","position":"top right","max":10,"reverse":false,"messages":[{"group":"toastie","message":"ham","type":"success","title":"","duration":2000,"speed":300,"customDuration":null},{"group":"toastie","message":"cheese","type":"info","title":"","duration":2500,"speed":300,"customDuration":null}]}}';

        $this->assertEquals($validJson, $this->binder->generateJs());

        $this->toaster->clear();
    }

    /** @test */
    public function it_generates_correct_component_html()
    {
        $this->toaster->group('toastie')->add('ham')->success()->add('cheese');

        $validComponent = '<notifications group="toastie" width="300px" position="top right" animation-type="velocity" :max="10" reverse="" ></notifications>';

        $this->assertEquals($validComponent, $this->binder->component());
    }

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
