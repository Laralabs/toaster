<?php

namespace Laralabs\Toaster;

class ToasterConverter
{
    /**
     * The namespace to put JS variable under.
     *
     * @var string
     */
    protected $namespace;

    /**
     * @var ToasterViewBinder
     */
    protected $viewBinder;

    public function __construct(ToasterViewBinder $viewBinder, $namespace = 'window')
    {
        $this->viewBinder = $viewBinder;
        $this->namespace = $namespace;
    }

    /**
     * Encode data to JSON and bind to the view.
     *
     * @param array $data
     *
     * @return string
     */
    public function put(array $data = [])
    {
        reset($data);
        $js = 'window.'.$this->namespace.' = window.'.$this->namespace.' || {};'.$this->namespace.'.'.key($data).' = ';
        $js = $js.json_encode($data[key($data)]);

        $this->viewBinder->bind($js);

        return $js;
    }
}
