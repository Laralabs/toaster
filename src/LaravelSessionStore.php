<?php

namespace Laralabs\Toaster;

use Illuminate\Session\Store;
use Laralabs\Toaster\Interfaces\SessionStore;

class LaravelSessionStore implements SessionStore
{
    /**
     * @var Store
     */
    private $session;

    /**
     * Create a new session store instance.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Flash a message to the session.
     *
     * @param $name
     * @param $data
     */
    public function flash($name, $data)
    {
        $this->session->flash($name, $data);
    }

    /**
     * Checks if a key is present and not null.
     *
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        $this->session->has($key);
    }

    /**
     * Get an item from the session.
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $this->session->get($key, $default);
    }


}
