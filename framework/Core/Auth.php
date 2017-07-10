<?php

namespace Framework\Core;


class Auth
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        return new Auth();
    }

    public static function hasLogin()
    {
        return self::getInstance()->isLogin();
    }

    public function login($username, $password)
    {
        if ($username === 'admin' && $password === '123') {
            $_SESSION['login'] = true;
            return true;
        }

        return false;
    }

    public function isLogin()
    {
        return isset($_SESSION['login']) && (bool)$_SESSION['login'];
    }

    public function logout()
    {
        if (isset($_SESSION['login'])) {
            unset($_SESSION['login']);
            return true;
        }

        return false;
    }
}