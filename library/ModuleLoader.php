<?php

namespace library;

class ModuleLoader
{
    private $_platformModulePath = null;
    public function __construct($platformRoot)
    {
        $this->_platformModulePath = $platformRoot . "/modules";
    }
    public function autoLoaderModules($platform)
    {
        foreach ($this->getModuleList() as $module) {
            new $module($platform);
        }
    }
    public function getModuleList()
    {
        $moduleList = array();
        if (is_dir($this->_platformModulePath)) {
            $modulesDir = scandir($this->_platformModulePath);
            foreach ($modulesDir as $module) {
                if ('.' == $module || '..' == $module) {
                    continue;
                }
                if (is_file($this->_platformModulePath . "/" . $module . "/routes/" . $module . ".php")) {
                    $moduleList[$module] = $module . "\\routes\\" . $module;
                }
            }
        }
        return $moduleList;
    }
}

