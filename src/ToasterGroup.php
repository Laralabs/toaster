<?php

namespace Laralabs\Toaster;

class ToasterGroup
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $properties;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $messages;

    public function __construct($name = 'default', $properties = null)
    {
        $this->name = $name;
        $this->properties = [
            'name'            => $name,
            'width'           => config('toaster.toast_width'),
            'classes'         => config('toaster.toast_classes'),
            'animation_type'  => config('toaster.animation_type'),
            'animation_name'  => null,
            'velocity_config' => 'velocity',
            'position'        => config('toaster.toast_position'),
            'max'             => config('toaster.max_toasts'),
            'reverse'         => config('toaster.reverse_order'),
        ];
        if (is_array($properties)) {
            $this->properties = array_merge($this->properties, $properties);
        }
        $this->messages = collect();
    }

    /**
     * @param $toast
     *
     * @return \Illuminate\Support\Collection
     */
    public function add($toast)
    {
        return $this->messages->push($toast);
    }

    /**
     * Update property.
     *
     * @param $key
     * @param $value
     *
     * @return \Laralabs\Toaster\Toaster
     */
    public function updateProperty($key, $value)
    {
        $this->properties[$key] = $value;

        return app('toaster')->flash();
    }

    /**
     * Update properties.
     *
     * @param $properties
     *
     * @return \Laralabs\Toaster\Toaster
     */
    public function updateProperties($properties)
    {
        foreach ($properties as $key => $value) {
            $this->properties[$key] = $value;
        }

        return app('toaster')->flash();
    }

    /**
     * Modify the most recently added message.
     *
     * @param array $overrides
     *
     * @throws \Exception
     *
     * @return \Laralabs\Toaster\Toaster
     */
    public function updateLastMessage($overrides = [])
    {
        if ($this->messages->count() > 0) {
            $this->messages->last()->update($overrides);

            return app('toaster')->flash();
        }

        throw new \Exception('Use the add() function to add a message before attempting to modify it');
    }
}
