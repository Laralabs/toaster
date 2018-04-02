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

    public function __construct($name = 'default')
    {
        $this->name = $name;
        $this->properties = [
            'name' => $name,
            'width' => config('toaster.toast_width'),
            'classes' => config('toaster.toast_classes'),
            'animation_type' => config('toaster.animation_type'),
            'animation_name' => null,
            'velocity_config' => 'velocity',
            'position' => config('toaster.toast_position'),
            'max' => config('toaster.max_toasts'),
            'reverse' => config('toaster.reverse_order')
        ];
        $this->messages = collect();
    }

    /**
     * @param $toast
     * @return $this
     */
    public function add($toast)
    {
        return $this->messages->push($toast);
    }

    public function updateProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }

    public function updateProperties($properties)
    {
        foreach ($properties as $key => $value) {
            $properties[$key] = $value;
        }
    }

    /**
     * Modify the most recently added message.
     *
     * @param array $overrides
     *
     * @return $this
     */
    public function updateLastMessage($overrides = [])
    {
        if ($this->messages->count() > 0) {
            $this->messages->last()->update($overrides);

            return $this->flash();
        }

        abort(500, 'Use the add() function to add a message before attempting to modify it');
    }
}