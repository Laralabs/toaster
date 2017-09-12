<?php

namespace Laralabs\Toaster\Interfaces;

interface SessionStore
{
    /**
     * Flash a message to the session.
     *
     * @param $name
     * @param $data
     */
    public function flash($name, $data);

    /**
     * Checks if a key is present and not null.
     *
     * @param $key
     * @return mixed
     */
    public function has($key);

    /**
     * Get an item from the session.
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null);
}
