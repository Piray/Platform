<?php

namespace routes;

class Platform
{
    private $_systemMenu = array();
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
        $this->app->get('/about', array($this, 'getAbout'));
        $this->app->get('/contact', array($this, 'getContact'));
    }
    public function getContact()
    {
        echo $this->ui->render('contact/contact.html.twig');
    }
    public function getAbout()
    {
        echo $this->ui->render('about/about.html.twig');
    }
    public function getIndex()
    {
        echo $this->ui->render('index/index.html.twig');
    }
    public function registPirayMenu($menuName, $linkAction)
    {
        $this->_systemMenu[] = array(
            'name' => $menuName,
            'action' => $linkAction
        );
        $this->ui->addGlobal('service_menu', $this->_systemMenu);
    }
    public function authenticate() // middleware to lock need to authenticate private page
    {
        $this->_login->loginValid();
    }
}
