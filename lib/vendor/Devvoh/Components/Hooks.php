<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  GetSet
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Hooks {

    protected $hooks = array();

    /**
     * Add hook referencing $closure to $event, returns false if $closure isn't a function
     *
     * @param null|string   $event
     * @param null|callable $closure
     *
     * @return bool|$this
     */
    public function add($event = null, $closure = null) {
        // Check if all data is given and correct
        if (!$event || !$closure || !is_callable($closure)) {
            return false;
        }

        // All good, add the event & closure to hooks
        $this->hooks[$event][] = $closure;

        // And return ourselves
        return $this;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $closures
     *
     * @param null $event
     * @param null $payload
     *
     * @return bool|$this
     */
    public function trigger($event = null, &$payload = null) {
        // Check if all data is given and correct
        if (!$event) {
            return false;
        }

        // Check if the event exists and has closures to call
        if (!isset($this->hooks[$event]) || count($this->hooks[$event]) == 0) {
            return false;
        }

        // All good, let's call those closures
        foreach ($this->hooks[$event] as $closure) {
            $closure($payload);
        }

        // And return ourselves
        return $this;
    }
}