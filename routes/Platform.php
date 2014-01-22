<?php

namespace routes;

class Platform
{
    private $_platformModule = null;
    private $_userApi = null;
    private $_mailApi = null;
    private $_login = null;
    public function __construct($app, $ui)
    {
        $this->app = $app;
        $this->ui = $ui;
        $this->helper = new \library\Helper($this);
        $this->session = new \library\Session("30 minutes");
        $this->app->add($this->session); // add PlatformSession to Middleware

        // init route module
        $this->_login = new \routes\Login($this);
        // init api module
        $this->_userApi = new \api\User($this);
        $this->_mailApi = new \api\Mailer($this);

        // platform routing
        $this->app->get('/', array($this, 'getIndex'))->name('index');
        $this->app->get('/contact', array($this, 'getContact'));
        $this->app->get('/apps', array($this, 'getApps'));
    }
    public function getApps()
    {
        echo $this->ui->render('apps/apps.html.twig', array(
            'page_header' => array('title' => 'Piray Apps', 'subtitle' => 'make you easy to use'),
            'modules' => $this->_platformModule
        ));
    }
    public function getContact()
    {
        echo $this->ui->render('contact/contact.html.twig', array(
            'page_header' => array('title' => 'Contact Piray', 'subtitle' => 'any problem we will solve it')
        ));
    }
    public function getIndex()
    {
        echo $this->ui->render('index/index.html.twig');
    }
    public function registPlatformModule($moduleName, $moduleLink, $moduleTitle, $moduleImage = null)
    {
        $this->_platformModule[] = array(
            'name' => $moduleName,
            'link' => $moduleLink,
            'title' => $moduleTitle,
            'image' => $moduleImage
        );
    }
    public function authenticate() // middleware to lock need to authenticate private page
    {
        $this->_login->loginValid();
    }
}

