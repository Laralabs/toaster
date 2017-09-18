<?php

namespace Laralabs\Toaster;

use Laralabs\Toaster\Interfaces\SessionStore;

class Toaster
{
    /**
     * Session Store.
     *
     * @var SessionStore
     */
    protected $session;

    /**
     * @var string
     */
    public $json;

    /**
     * @var \Illuminate\Support\Collection
     */
    public $messages;

    /**
     * @var int
     */
    public $lifetime;

    /**
     * @var int
     */
    public $interval;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var string
     */
    public $position;

    public function __construct(SessionStore $session)
    {
        $this->session = $session;
        $this->messages = collect();
        $this->lifetime = config('toaster.toast_lifetime');
        $this->interval = config('toaster.toast_interval');
        $this->limit = config('toaster.max_toasts');
        $this->position = config('toaster.toast_position');
    }

    /**
     * Set message info theme.
     *
     * @return $this
     */
    public function info()
    {
        return $this->updateLastMessage(['theme' => 'info']);
    }

    /**
     * Set message success theme.
     *
     * @return $this
     */
    public function success()
    {
        return $this->updateLastMessage(['theme' => 'success']);
    }

    /**
     * Set message error theme.
     *
     * @return $this
     */
    public function error()
    {
        return $this->updateLastMessage(['theme' => 'error']);
    }

    /**
     * Set message warning theme.
     *
     * @return $this
     */
    public function warning()
    {
        return $this->updateLastMessage(['theme' => 'warning']);
    }

    /**
     * Set message close button flag to true.
     *
     * @return $this
     */
    public function important()
    {
        return $this->updateLastMessage(['closeBtn' => true]);
    }

    /**
     * Set message title.
     *
     * @param $value
     *
     * @return Toaster
     */
    public function title($value)
    {
        return $this->updateLastMessage(['title' => $value]);
    }

    /**
     * Set message expiry time.
     *
     * @param $value
     *
     * @return Toaster
     */
    public function expires($value)
    {
        if (is_int($value)) {
            return $this->updateLastMessage(['expires' => $value]);
        }

        abort(500, 'Argument passed to expires() must be a valid integer');
    }

    /**
     * Add a message to the toaster.
     *
     * @param string|null $message
     * @param bool|false  $closeBtn
     * @param string      $theme
     * @param string      $title
     * @param string|null $expires
     *
     * @return $this
     */
    public function add($message, $theme = 'info', $closeBtn = false, $title = '', $expires = null)
    {
        if (!$message instanceof Toast) {
            $message = new Toast(compact('message', 'theme', 'closeBtn', 'title', 'expires'));
        }

        $this->messages->push($message);

        return $this->flash();
    }

    /**
     * Updates the previous message.
     *
     * @param array $attributes
     *
     * @return Toaster
     */
    public function update($attributes)
    {
        return $this->updateLastMessage($attributes);
    }

    /**
     * Modify the most recently added message.
     *
     * @param array $overrides
     *
     * @return $this
     */
    protected function updateLastMessage($overrides = [])
    {
        if ($this->messages->count() > 0) {
            $this->messages->last()->update($overrides);

            return $this->flash();
        }

        abort(500, 'Use the add() function to add a message before attempting to modify it');
    }

    /**
     * Clear all registered messages.
     *
     * @return $this
     */
    public function clear()
    {
        $this->messages = collect();

        return $this;
    }

    /**
     * Set toast expiry time values.
     *
     * @return $this
     */
    protected function setExpires()
    {
        $expires = $this->lifetime;

        foreach ($this->messages as $toast) {
            if ($toast->expires === null) {
                $toast->expires = $expires;
            }
            $expires = $expires + $this->interval;
        }

        return $this;
    }

    /**
     * Flash all messages to the session.
     */
    protected function flash()
    {
        $this->setExpires();

        $this->session->flash('toaster', [
            'data' => [
                'lifetime'  => $this->lifetime,
                'maxToasts' => $this->limit,
                'messages'  => $this->messages->toArray(),
                'position'  => $this->position,
            ],
        ]);

        return $this;
    }
}
