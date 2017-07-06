<?php

namespace Parable\Tests\TestClasses;

class Config2 implements
    \Parable\Framework\Interfaces\Config
{
    /**
     * @return array
     */
    public function get()
    {
        return [
            'setting' => 'secondary value',
            'also'    => 'this one',
        ];
    }
}
