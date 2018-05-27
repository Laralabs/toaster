<?php

namespace Laralabs\Toaster;

class Toast
{
    /**
     * The group it belongs to.
     *
     * @var string
     */
    public $group;

    /**
     * The body of the message.
     *
     * @var string
     */
    public $message;

    /**
     * The message theme.
     *
     * @var string
     */
    public $type = 'info';

    /**
     * The message title.
     *
     * @var null
     */
    public $title = '';

    /**
     * The message expiry time value.
     *
     * @var null
     */
    public $duration;

    /**
     * @var int
     */
    public $speed;

    /**
     * @var
     */
    public $customDuration;

    /**
     * Create a new message instance.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $attributes['customDuration'] = isset($attributes['duration']) ? true : false;
        $attributes['duration'] = isset($attributes['duration']) ? $attributes['duration'] : config('toaster.toast_lifetime');
        $attributes['speed'] = isset($attributes['speed']) ? $attributes['speed'] : config('toaster.animation_speed');

        $this->update($attributes);
    }

    /**
     * Update the attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function update($attributes = [])
    {
        $attributes = array_filter($attributes);

        foreach ($attributes as $key => $attribute) {
            $this->$key = $attribute;
        }

        return $this;
    }
}
