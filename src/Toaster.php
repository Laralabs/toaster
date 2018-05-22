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
    protected $currentGroup;

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
        $this->currentGroup = 'default';
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
     * @param $value string
     *
     * @return Toaster
     */
    public function title(string $value)
    {
        $this->groups->last()->updateLastMessage(['title' => $value]);

        return $this;
    }

    /**
     * Set message as important.
     *
     * @return $this
     */
    public function important()
    {
        $this->groups->last()->updateLastMessage(['duration' => -1, 'customDuration' => true]);

        return $this;
    }

    /**
     * Set message duration.
     *
     * @param $value int
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function duration(int $value)
    {
        $this->groups->last()->updateLastMessage(['duration' => $value, 'customDuration' => true]);

        return $this;
    }

    /**
     * Set message animation speed.
     *
     * @param $value int
     * @return $this
     */
    public function speed(int $value)
    {
        $this->groups->last()->updateLastMessage(['speed' => $value]);

        return $this;
    }

    /**
     * Add a message to the toaster.
     *
     * @param $message string
     * @param null $title
     * @param null $properties
     * @return Toaster
     * @throws \Exception
     */
    public function add(string $message, $title = null, $properties = null)
    {
        if (is_array($properties)) {
            $properties['message'] = $message;
            $properties['title'] = is_null($title) ? isset($properties['title']) ? $properties['title'] : $title : $title;
            $properties['group'] = isset($properties['group']) ? $properties['group'] : $this->currentGroup;
            $group = $properties['group'];
            $message = new Toast($properties);
        } else {
            $group = $this->currentGroup;
            $message = new Toast(compact('message', 'title', 'group'));
        }

        if ($this->groups->count() < 1) {
            $this->group($this->currentGroup);
        }

        if ($this->groups->where('name', '=', $group)->first()->add($message)) {
            return $this->flash();
        } else {
            throw new \Exception('No group found with the specified name');
        }
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
        $this->currentGroup = $name;

        $this->groups->push($group);

        return $this;
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
     * Clear all registered groups.
     *
     * @return $this
     */
    public function clear()
    {
        $this->groups = collect();
        $this->flash();

        return $this;
    }

    /**
     * Stagger messages with lifetime and interval
     *
     *
     * @param bool $all
     */
    protected function stagger($all = true)
    {
        $current = $this->lifetime - $this->interval;

        foreach ($this->groups->all() as $group) {
            $current = $all ? $current : $this->lifetime - $this->interval;
            foreach ($group->messages->all() as $message) {
                $current = $current + $this->interval;
                $message->duration = $message->customDuration ? $message->duration : $current;
                $current = $message->customDuration ? $current - $this->interval : $current;
            }
        }
    }

    /**
     * Flash all messages to the session.
     *
     * @return $this
     */
    public function flash()
    {
        if (config('toaster.toast_stagger')) {
            config('toaster.toast_stagger_all') ? $this->stagger() : $this->stagger(false);
        }

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
            $payload['data'][$group->name] = array_merge($group->properties, ['messages' => $group->messages->toArray()]);
        }

        return $payload;
    }
}
