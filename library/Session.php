<?php

namespace library;

class Session extends \Slim\Middleware
{
    private $_sessionExpireInSec = 0;
    public function __construct($expireTime = null)
    {
        if (null !== $expireTime) {
            $this->_sessionExpireInSec = strtotime($expireTime) - time();
        }
        session_cache_limiter(false);
    }
    public function call()
    {
        $this->startSession();
        $this->next->call();
    }
    private function startSession()
    {
        if ($this->_sessionExpireInSec == 0) {
            $this->_sessionExpireInSec = ini_get('session.gc_maxlifetime');
        } else {
            ini_set('session.gc_maxlifetime', $this->_sessionExpireInSec);
        }

        if (empty($_COOKIE['PHPSESSID'])) {
            session_set_cookie_params($this->_sessionExpireInSec);
            session_start();
        } else {
            session_start();
            setcookie('PHPSESSID', session_id(), time() + $this->_sessionExpireInSec);
        }
    }
    public function endSession()
    {
        session_destroy();
    }
    public function unsetVariable($key)
    {
        unset($_SESSION[$key]);
    }
    public function setVariable($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public function getVariable($key)
    {
        if (isset($_SESSION[$key]) && !empty($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }
}

