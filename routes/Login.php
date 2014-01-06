<?php

namespace routes;

class Login extends \library\Module
{
    public function init()
    {
        /*
         * /login               get,post
         * /logout              get
         */
        $this->app->get('/login', array($this, 'getLogin'))->name('login');
        $this->app->post('/login', array($this, 'postLogin'));
        $this->app->get('/logout', array($this, 'getLogout'))->name('logout');
    }
    public function getLogin()
    {
        $loginMsg = $this->session->getVariable('login_msg');
        $this->session->unsetVariable('login_msg');
        echo $this->ui->render('login/login.html.twig', array('login_msg' => $loginMsg));
    }
    public function postLogin()
    {
        $request = $this->app->request();
        if ("" !== $request->post('username') && "" !== $request->post('password')) {
            $username = $request->post('username');
            $password = $request->post('password');
            if ($this->userValid($username, $password)) {
                $this->session->unsetVariable('login_msg');
                $this->setLogin($username);
                $requestUri = $this->session->getVariable('request_uri');
                if (null !== $requestUri) {
                    $this->app->redirect($requestUri);
                } 
                $this->app->redirect($this->app->urlFor('index'));
            } else {
                $this->session->setVariable('login_msg', "Username or Password Error");
                $this->app->redirect($this->app->urlFor('login'));
            }
        } else {
            $this->session->setVariable('login_msg', "Input Error");
            $this->app->redirect($this->app->urlFor('login'));
        }
    }
    public function getLogout()
    {
        $this->session->endSession();
        $this->app->redirect($this->app->urlFor('index'));
    }
    private function userValid($username, $password) 
    {
        $userRow = \ORM::forTable('user')
            ->where('name', $username)
            ->where('password', $password)
            ->findOne();

        if (false !== $userRow) {
            return true;
        }
        return false;
    }
    private function setLogin($name)
    {
        $userData = $this->getUserData($name);
        if (null !== $userData) {
            $this->session->setVariable('login', true);
            $this->session->setVariable('user', $userData['name']);
            $this->session->setVariable('level', $userData['level']);
            return true;
        }
        return false;
    }
    private function getUserData($name)
    {
        $userData = \ORM::forTable('user')->where('name', $name)->findOne();
        if (false !== $userData) {
            return $userData;
        }
        return null;
    }
    public function loginValid()
    {
        $request = $this->app->request();
        if ($this->session->getVariable('login')) {
            // check already login and pass
        } else {
            // query to login
            $this->session->setVariable('request_uri', $request->getRootUri() . $request->getResourceUri());
            $this->app->redirect($this->app->urlFor('login'));
        }
    }
}

