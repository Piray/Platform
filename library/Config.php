<?php

namespace library;

class Config
{
    private $_config = null;
    public function __construct($configFilePath)
    {
        $configJsonData = file_get_contents($configFilePath);
        $this->_config = json_decode($configJsonData, true);
    }
    public function getPlatformSetting()
    {
        return $this->_config;
    }
    public function getSlimSetting($mode)
    {
        if (isset($this->_config['slim'][$mode])) {
            return $this->_config['slim'][$mode];
        }
        return null;
    }
    public function getTwigSetting($mode)
    {
        if (isset($this->_config['twig'][$mode])) {
            return $this->_config['twig'][$mode];
        }
        return null;
    }
    public function getIdiormSetting($mode)
    {
        if (isset($this->_config['idiorm'][$mode])) {
            return $this->_config['idiorm'][$mode];
        }
        return null;
    }
    public function getDbSetting($dbName)
    {
        if (isset($this->_config['db'][$dbName])) {
            return $this->_config['db'][$dbName];
        }
        return null;
    }
}

