<?php

namespace Laralabs\Toaster\Tests;

use Laralabs\Toaster\Toaster;
use Laralabs\Toaster\ToasterServiceProvider;
use Laralabs\Toaster\ToasterViewBinder;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var
     */
    protected $session;

    /**
     * @var
     */
    protected $toaster;

    /**
     * @var
     */
    protected $binder;

    /**
     * @var
     */
    public $limit;

    /**
     * @var
     */
    public $position;

    /**
     * @var
     */
    public $lifetime;

    /**
     * @var
     */
    public $interval;

    public function setUp()
    {
        parent::setUp();

        $this->session = app('Laralabs\Toaster\Interfaces\SessionStore');

        $this->toaster = new Toaster($this->session);
        $this->binder = new ToasterViewBinder();

        $this->limit = config('toaster.max_toasts');
        $this->position = config('toaster.toast_position');
        $this->lifetime = config('toaster.toast_lifetime');
        $this->interval = config('toaster.toast_interval');
    }

    protected function getPackageProviders($app)
    {
        return [
            ToasterServiceProvider::class,
        ];
    }
}
