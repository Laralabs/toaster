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
    public $groups;

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
        $this->groups = collect();
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
        $this->groups->last()->updateLastMessage(['type' => 'info']);

        return $this;
    }

    /**
     * Set message success theme.
     *
     * @return $this
     */
    public function success()
    {
        $this->groups->last()->updateLastMessage(['type' => 'success']);

        return $this;
    }

    /**
     * Set message error theme.
     *
     * @return $this
     */
    public function error()
    {
        $this->groups->last()->updateLastMessage(['type' => 'error']);

        return $this;
    }

    /**
     * Set message warning theme.
     *
     * @return $this
     */
    public function warning()
    {
        $this->groups->last()->updateLastMessage(['type' => 'warn']);

        return $this;
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
        $this->groups->last()->updateLastMessage(['title' => $value]);

        return $this;
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

        abort(500, 'Argument passed to expires() must be a valid integer (milliseconds)');
    }

    /**
     * Add a message to the toaster.
     *
     * @param $message
     * @param null $title
     * @param null $properties
     * @param null $group
     * @return Toaster
     * @throws \Exception
     */
    public function add($message, $title = null, $properties = null, $group = null)
    {
        if (!$message instanceof Toast) {
            if (is_array($properties)) {
                $properties['message'] = $message;
                $properties['title'] = $title;
                $properties['group'] = $group;
                $message = new Toast($properties);
            } else {
                $message = new Toast(compact('message', 'title', 'group'));
            }
        }

        if (!is_null($group)) {
            if ($this->groups->where('name', '=', $group)->first()->add($message)) {
                return $this->flash();
            } else {
                throw new \Exception('No group found with the specified name');
            }

        }

        $this->groups->last()->add($message);

        return $this->flash();
    }

    /**
     * Create a new group.
     *
     * @param $name
     * @return $this
     */
    public function group($name)
    {
        $group = new ToasterGroup($name);

        return $this->groups->push($group);
    }

    /**
     * Set group width.
     *
     * @param string $width
     * @return mixed
     */
    public function width(string $width)
    {
        $this->groups->last()->updateProperty('width', $width);

        return $this;
    }

    /**
     * Set group classes.
     *
     * @param array $classes
     * @return mixed
     */
    public function classes(array $classes)
    {
        $this->groups->last()->updateProperty('classes', $classes);

        return $this;
    }

    /**
     * Set group animation type.
     *
     * @param string $animationType
     * @return mixed
     */
    public function animationType(string $animationType)
    {
        $this->groups->last()->updateProperty('animation_type', $animationType);

        return $this;
    }

    /**
     * Set group animation name.
     *
     * @param string $animationName
     * @return mixed
     */
    public function animationName(string $animationName)
    {
        $this->groups->last()->updateProperty('animation_name', $animationName);

        return $this;
    }

    /**
     * Set group velocity config.
     *
     * @param string $velocityConfig
     * @return mixed
     */
    public function velocityConfig(string $velocityConfig)
    {
        $this->groups->last()->updateProperty('velocity_config', $velocityConfig);

        return $this;
    }

    /**
     * Set group position.
     *
     * @param string $position
     * @return mixed
     */
    public function position(string $position)
    {
        $this->groups->last()->updateProperty('position', $position);

        return $this;
    }

    /**
     * Set group max.
     *
     * @param int $max
     * @return mixed
     */
    public function max(int $max)
    {
        $this->groups->last()->updateProperty('max', $max);

        return $this;
    }

    /**
     * Set group reverse order.
     *
     * @param bool $reverse
     * @return mixed
     */
    public function reverse(bool $reverse)
    {
        $this->groups->last()->updateProperty('reverse', $reverse);

        return $this;
    }

    /**
     * Set group properties.
     *
     * @param array $properties
     * @return mixed
     */
    public function properties(array $properties)
    {
        $this->groups->last()->updateProperties($properties);

        return $this;
    }

    /**
     * Updates the previous message.
     *
     * @param array $attributes
     *
     * @return Toaster
     */
    public function update(array $attributes)
    {
        $this->groups->last()->updateLastMessage($attributes);

        return $this;
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
     * Clear all registered groups.
     *
     * @return $this
     */
    public function clear()
    {
        $this->groups = collect();

        return $this;
    }

    /**
     * Set toast expiry time values.
     *
     * @return $this
     *
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
    }*/

    /**
     * Flash all messages to the session.
     */
    protected function flash()
    {
        //$this->setExpires();

        $this->session->flash('toaster', $this->parse());

        return $this;
    }

    /**
     * Parse groups and messages into array.
     *
     * @return array
     */
    protected function parse()
    {
        $payload = ['data' => []];

        foreach ($this->groups->all() as $group) {
            $properties = array_merge($group->properties, $group->messages->toArray());
            $payload['data'][$group->name] = $properties;
        }

        return $payload;
    }
}
