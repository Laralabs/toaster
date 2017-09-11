<?php

namespace Laralabs\Toaster;

use Illuminate\Contracts\Events\Dispatcher;
use Laralabs\Toaster\Interfaces\ViewBinder;

class ToasterViewBinder implements ViewBinder
{
    /**
     * Event Dispatcher
     *
     * @var Dispatcher
     */
    private $event;

    /**
     * Name of the view to bind variables to
     *
     * @var
     */
    private $views;

    public function __construct(Dispatcher $event, $views = [])
    {
        $this->event = $event;
        $this->views = str_replace('/', '.', (array)$views);
    }

    /**
     * Bind the JavaScript variable to the specified view
     *
     * @param string $js
     * @return null
     */
    public function bind($js)
    {
        foreach($this->views as $view) {
            $this->event->listen("composing: {$view}", function() use ($js) {
                echo "<script>{$js}</script>";
            });
        }
    }
}