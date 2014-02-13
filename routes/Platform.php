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
        $this->_deviceApi = new \api\Device($this);
        $this->_userApi = new \api\User($this);
        $this->_mailApi = new \api\Mailer($this);

        // hook all route add user session data
        $this->app->hook('slim.before.router', array($this, 'dispatchUiGlobalData'));

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
    public function dispatchUiGlobalData()
    {
        // route data
        $request = $this->app->request();
        $this->ui->addGlobal('base_url', $request->getRootUri());
        $this->ui->addGlobal('resource_url', $request->getResourceUri());

        // session data
        if ($this->session->getVariable('login')) {
            $this->ui->addGlobal('login_user', array(
                'name' => $this->session->getVariable('user'),
                'level' => $this->session->getVariable('level')
            ));
        }
    }
    public function authenticate() // middleware to lock need to authenticate private page
    {
        $this->_login->loginValid();
    }
}

