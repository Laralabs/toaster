<?php

namespace Laralabs\Toaster\Tests;

use Laralabs\Toaster\Toaster;
use Mockery as Mockery;

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
        $this->assertSessionIsFlashed();
    }

    /** @test */
    public function it_displays_multiple_toast()
    {
        $this->toaster->add('Beans on Toast');
        $this->toaster->add('Egg on Toast');

        $this->assertCount(2, $this->toaster->messages);

        $this->toaster->toast();
        $this->assertSessionIsFlashed();
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

        $this->assertSessionIsFlashed();
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

        $this->assertSessionIsFlashed();
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

        $this->assertSessionIsFlashed();
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

        $this->assertSessionIsFlashed();
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

        $this->assertSessionIsFlashed();
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

        $this->assertSessionIsFlashed();
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

    protected function assertSessionIsFlashed($times = 1)
    {
        $this->session
            ->shouldHaveReceived('flash')
            ->with('toaster', $this->toaster->messages)
            ->times($times);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
