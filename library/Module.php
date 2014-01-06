<?php

namespace library;

abstract class Module
{
    public function __construct($platform)
    {
        $this->platform = $platform;
        $this->app = $platform->app;
        $this->ui = $platform->ui;
        $this->helper = $platform->helper;
        $this->session = $platform->session;
        $this->init();
    }
    abstract protected function init();
}

