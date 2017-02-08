<?php

namespace Config;

class App extends \Parable\Framework\Config\Base
{
    /** @var null|int */
    protected $sortOrder = 0;

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            'app' => [
                'title'      => 'Parable'
            ],
            'session' => [
                'autoEnable' => true,
            ],
            'initLocations'  => [
                'app/Init',
            ],
        ];
    }
}
