<?php

namespace AllenJB\Utilities;

class Config
{

    protected $dir;

    protected $name;

    protected $config = [];


    public function __construct($configDir, $configName)
    {
        $this->dir = realpath($configDir) . '/';
        $this->name = $configName;

        $this->config = $this->getConfig();
        if (defined('ENVIRONMENT')) {
            $this->config = array_merge($this->config, $this->getConfig(ENVIRONMENT));
        }
    }


    private function getConfig($environment = '')
    {
        $path = $this->dir . $environment . '/' . $this->name . '.php';
        if (! (file_exists($path) && is_readable($path))) {
            return [];
        }
        $config = require($path);
        if (! is_array($config)) {
            throw new \UnexpectedValueException("Invalid configuration file (does not return array)");
        }
        return $config;
    }


    public function get($key)
    {
        if (! array_key_exists($key, $this->config)) {
            throw new \InvalidArgumentException("No config entry exists with key: {$key} in {$this->name}");
        }
        return $this->config[$key];
    }


    public function all()
    {
        return $this->config;
    }

}
