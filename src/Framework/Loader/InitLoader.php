<?php

namespace Parable\Framework\Loader;

class InitLoader
{
    /**
     * Load array of init classes.
     *
     * @param string[] $initClasses
     *
     * @throws \Parable\DI\Exception
     */
    public function load(array $initClasses)
    {
        foreach ($initClasses as $initClass) {
            \Parable\DI\Container::create($initClass);
        }
    }
}
