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
     * Config: js_namespace
     *
     * @var string
     */
    protected $namespace;

    public function __construct(Router $router, Store $store)
    {
        $this->router = $router;
        $this->store = $store;

        $this->namespace = config('toaster.js_namespace');
    }

    public function generateJs()
    {
        if ($this->store->has('toaster')) {
            $data = $this->store->get('toaster');
            reset($data);
            $js = 'window.'.$this->namespace.' = window.'.$this->namespace.' || {};'.$this->namespace.'.'.key($data).' = ';
            $js = $js.json_encode($data[key($data)]);

            $this->store->forget('toaster');

            return $js;
        }

        return 'window.'.$this->namespace.' = window.'.$this->namespace.' || {};'.$this->namespace.'.data = {};';
    }

    /**
     * Return the JavaScript variable to the view.
     *
     * @return null
     */
    public function bind()
    {
        return '<script type="text/javascript">'.$this->generateJs().'</script>';
    }
}
