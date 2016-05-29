<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Config extends \Devvoh\Components\GetSet {

    /** @var \Devvoh\Parable\Tool */
    protected $tool;

    /**
     * @param \Devvoh\Parable\Tool $tool
     */
    public function __construct(
        \Devvoh\Parable\Tool $tool
    ) {
        $this->tool = $tool;
        $this->setResource('config');
    }

    /**
     * Load the config if it exists, and merge custom into it if it exists
     *
     * @return $this
     * @throws \Devvoh\Components\Exception
     */
    public function load() {
        $configFile = $this->tool->getDir('app/config/config.ini');
        $customFile =$this->tool->getDir('app/config/custom.ini');

        if (file_exists($configFile)) {
            $configData = parse_ini_file($configFile, true);
        } else {
            throw new \Devvoh\Components\Exception('Required file config.ini not found');
        }
        if (file_exists($customFile)) {
            $configData = parse_ini_file($customFile) + $configData;
        }
        $this->setAll($configData);
        return $this;
    }

    /**
     * Return the value in $key as a boolean
     *
     * @param string $key
     *
     * @return bool
     */
    public function getBool($key) {
        return (bool)$this->get($key);
    }

}