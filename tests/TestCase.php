<?php

namespace Laralabs\Toaster\Tests;

use Laralabs\Toaster\Toaster;
use Laralabs\Toaster\ToasterServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var
     */
    protected $session;

    /**
     * @var \Laralabs\Toaster\Toaster
     */
    protected $toaster;

    /**
     * @var \Laralabs\Toaster\ToasterViewBinder
     */
    protected $binder;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var string
     */
    public $position;

    /**
     * @var int
     */
    public $lifetime;

    /**
     * @var int
     */
    public $interval;

    /**
     * @var string
     */
    public $width;

    /**
     * @var array
     */
    public $classes;

    /**
     * @var bool
     */
    public $reverse;

    /**
     * @var
     */
    public $animationType;

    /**
     * @var
     */
    public $animationSpeed;

    public function setUp(): void
    {
        parent::setUp();

        $this->session = app('Laralabs\Toaster\Interfaces\SessionStore');

        $this->toaster = new Toaster($this->session);
        $this->binder = app('toasterViewBinder');

        $this->limit = config('toaster.max_toasts');
        $this->position = config('toaster.toast_position');
        $this->lifetime = config('toaster.toast_lifetime');
        $this->interval = config('toaster.toast_interval');
        $this->width = config('toaster.toast_width');
        $this->classes = config('toaster.toast_classes');
        $this->reverse = config('toaster.reverse_order');
        $this->animationType = config('toaster.animation_type');
        $this->animationSpeed = config('toaster.animation_speed');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    protected function getPackageProviders($app)
    {
        return [
            ToasterServiceProvider::class,
        ];
    }
}
