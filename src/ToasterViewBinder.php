<?php

namespace Laralabs\Toaster;

use Illuminate\Routing\Router;
use Illuminate\Session\Store;
use Laralabs\Toaster\Interfaces\ViewBinder;

class ToasterViewBinder implements ViewBinder
{
    /**
     * Router.
     *
     * @var \Illuminate\Routing\Router
     */
    private $router;

    /**
     * Session Store.
     *
     * @var \Illuminate\Session\Store
     */
    private $store;

    /**
     * Config: js_namespace.
     *
     * @var string
     */
    protected $namespace;

    public function __construct(Router $router, Store $store)
    {
        $this->router = $router;
        $this->store = $store;

        $this->namespace = 'toaster';
    }

    /**
     * Generates a JS variable.
     *
     * @return mixed
     */
    public function generateJs()
    {
        if ($this->store->has('toaster')) {
            $data = $this->store->get('toaster');
            reset($data);
            $js = 'window.'.$this->namespace.' = window.'.$this->namespace.' || {};'.$this->namespace.'.'.key($data).' = ';
            $js = $js.json_encode($data[key($data)]);

            return $js;
        }

        return 'window.'.$this->namespace.' = window.'.$this->namespace.' || {};'.$this->namespace.'.data = {};';
    }

    /**
     * Generate component data.
     *
     * @return array
     */
    protected function generateComponents()
    {
        $components = [];

        if ($this->store->has('toaster')) {
            $data = $this->store->get('toaster');

            foreach ($data['data'] as $group => $properties) {
                unset($properties['messages']);
                $components[$group] = $properties;
            }

            $this->store->forget('toaster');

            return $components;
        }

        return $components;
    }

    /**
     * Return the JavaScript variable to the view.
     *
     * @return string
     */
    public function bind()
    {
        return '<script type="text/javascript">'.$this->generateJs().'</script>';
    }

    /**
     * Generate vue-notification component markup.
     *
     * @return string
     */
    public function component()
    {
        $components = '';

        foreach ($this->generateComponents() as $group => $props) {
            $components = $components.' '.
                '<toaster-group '.
                (isset($props['name']) ? 'group="'.$props['name'].'" ' : '').
                (isset($props['width']) ? 'width="'.$props['width'].'" ' : '').
                (isset($props['position']) ? 'position="'.$props['position'].'" ' : '').
                (isset($props['animation_type']) ? 'animation-type="'.$props['animation_type'].'" ' : '').
                (isset($props['max']) ? ':max="'.$props['max'].'" ' : '').
                (isset($props['reverse']) ? 'reverse="'.$props['reverse'].'" ' : '').
                '>'.'</toaster-group>'.PHP_EOL;
        }

        return $components.'<toaster-logic></toaster-logic>';
    }
}
