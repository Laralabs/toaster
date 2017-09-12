<?php

namespace Laralabs\Toaster\Tests;

use Session;
use Laralabs\Toaster\Toaster;

class ToasterTest extends TestCase
{
    /** @test */
    public function toaster_function_returns_toaster_instance()
    {
        $toaster = toaster();

        $this->assertInstanceOf(Toaster::class, $toaster);
    }

    /** @test */
    public function it_can_interact_with_a_message_as_an_array()
    {
        $this->toaster->add('Beans on Toast');

        $this->assertEquals('Beans on Toast', $this->toaster->messages[0]['message']);
    }

    /** @test */
    public function it_displays_default_toast()
    {
        $this->toaster->add('Beans on Toast');

        $this->assertCount(1, $this->toaster->messages);

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Beans on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
        $this->assertSessionHas('toaster', json_encode($this->toaster->messages));
    }

    /** @test */
    public function it_displays_multiple_toast()
    {
        $this->toaster->add('Beans on Toast');
        $this->toaster->add('Egg on Toast');

        $this->assertCount(2, $this->toaster->messages);

        $this->toaster->toast();
    }

    /** @test */
    public function it_sets_null_expires()
    {
        $this->toaster->add('Beans on Toast');
        $this->assertCount(1, $this->toaster->messages);

        $toast = $this->toaster->messages[0];

        $this->assertEquals(null, $toast->expires);
        $this->toaster->toast();
        $this->assertEquals($this->lifetime, $toast->expires);
    }

    /** @test */
    public function it_sets_custom_expires()
    {
        $this->toaster->add('Information on Toast')->expires(5000);

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Information on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(5000, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_sets_multiple_null_expires_with_interval()
    {
        $this->toaster->add('Beans on Toast');
        $this->toaster->add('Egg on Toast');
        $this->toaster->add('This package is toastier');
        $this->toaster->toast();

        $this->assertCount(3, $this->toaster->messages);
        $expires = $this->lifetime;
        foreach ($this->toaster->messages as $toast) {
            $this->assertEquals($expires, $toast->expires);
            $expires = $expires + $this->interval;
        }
    }

    /** @test */
    public function it_sets_multiple_null_expires_with_interval_and_retains_custom_values()
    {
        $this->toaster->add('Beans on Toast');
        $this->toaster->add('Egg on Toast')->expires(9000);
        $this->toaster->add('This package is toastier');
        $this->toaster->toast();

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

    /** @test */
    public function it_generates_correctly_structured_json()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->add('Egg on Toast')->toast();

        $this->assertCount(2, $this->toaster->messages);

        $validJson = 'window.toaster = window.toaster || {};toaster.data = {"lifetime":2000,"maxToasts":10,"messages":[{"message":"Beans on Toast","theme":"success","closeBtn":false,"title":"","expires":2000},{"message":"Egg on Toast","theme":"info","closeBtn":false,"title":"","expires":2500}],"position":"top right"}';
        $this->assertEquals($validJson, $this->toaster->json);
    }

    /** @test */
    public function it_displays_info_toast()
    {
        $this->toaster->add('Info on Toast')->info();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Info on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_displays_success_toast()
    {
        $this->toaster->add('Success on Toast')->success();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Success on Toast', $toast->message);
        $this->assertEquals('success', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_displays_error_toast()
    {
        $this->toaster->add('Error on Toast')->error();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Error on Toast', $toast->message);
        $this->assertEquals('error', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_displays_warning_toast()
    {
        $this->toaster->add('Warning on Toast')->warning();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Warning on Toast', $toast->message);
        $this->assertEquals('warning', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_sets_close_button()
    {
        $this->toaster->add('Important Information on Toast')->important();

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Important Information on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(true, $toast->closeBtn);
        $this->assertEquals('', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_sets_toast_title()
    {
        $this->toaster->add('Information on Toast')->title('Toast is good for you');

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Information on Toast', $toast->message);
        $this->assertEquals('info', $toast->theme);
        $this->assertEquals(false, $toast->closeBtn);
        $this->assertEquals('Toast is good for you', $toast->title);
        $this->assertEquals(null, $toast->expires);

        $this->toaster->toast();
    }

    /** @test */
    public function it_can_update_last_message_with_add_function()
    {
        $this->toaster()->add('Beans on Toast')->success();
        $this->toaster()->add('error', true, 'It now has a title', 5000);

        $toast = $this->toaster->messages[0];

        $this->assertEquals('Beans on Toast', $toast->message);
        $this->assertEquals('error', $toast->theme);
        $this->assertEquals(true, $toast->closeBtn);
        $this->assertEquals('It now has a title', $toast->title);
        $this->assertEquals(5000, $toast->expires);

        $this->toaster()->toast();
    }

    /** @test */
    public function it_clears_message_collection()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->add('Egg on Toast')->toast();

        $this->assertCount(2, $this->toaster->messages);

        $this->toaster->clear();

        $this->assertCount(0, $this->toaster->messages);
    }

    /** @test */
    public function it_can_flash_data_to_session_store()
    {
        $this->toaster->add('Beans on Toast')->success();
        $this->toaster->add('Egg on Toast');

        $this->session->flash('toaster', $this->toaster->messages);

        $this->assertSessionHas('toaster', json_encode($this->toaster->messages));
    }

    /** @test */
    public function it_aborts_expires_non_integer()
    {
        $this->expectExceptionMessage('Argument passed to expires() must be a valid integer');

        $this->toaster->add('Beans on Toast')->expires('five minutes')->toast();
    }

    /** @test */
    public function it_aborts_missing_message()
    {
        $this->expectExceptionMessage('Provide a message to the add() function before attempting to modify it');

        $this->toaster->add()->toast();
    }

    /** @test */
    public function it_aborts_editing_non_message()
    {
        $this->expectExceptionMessage('Use the add() function to add a message before attempting to modify it');

        $this->toaster->success()->toast();
    }

    protected function assertSessionHas($name, $value = null)
    {
        $this->assertTrue(Session::has($name), "Session doesn't contain '$name'");
        if($value)
        {
            $this->assertEquals($value, Session::get($name), "Session '$name' are not equal to $value");
        }
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
