<?php

namespace Laralabs\Toaster;

class Toast implements \ArrayAccess
{
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
    public $theme = 'info';

    /**
     * Whether the message should auto-hide.
     *
     * @var bool
     */
    public $closeBtn = false;

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
    public $expires = null;

    /**
     * Create a new message instance.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->update($attributes);
    }

    /**
     * Update the attributes.
     *
     * @param  array $attributes
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


    /**
     * Whether the given offset exists.
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Fetch the offset.
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Assign the offset.
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the offset.
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        //
    }
}
