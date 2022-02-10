<?php

namespace Blog\Controllers;

class SessionController
{

    public $flash = [];


    public function __construct()
    {
        $this->sessionStart();
    }

    public static function sessionStart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getFlashMessage(string $key, $default = null)
    {
        if (!isset($this->flash[$key])) {
            $message = $this->get($key, $default);
            unset($_SESSION[$key]);
            $this->flash[$key] = $message;
        }

        return $this->flash[$key];
    }

    public static function set(string $name, $value, ?string $key = null)
    {
        if ($key == null) {
            $_SESSION[$name] = $value;
        } else {
            $_SESSION[$key][$name] = $value;
        }
        return true;
    }

    public static function get(string $name, ?string $key = null)
    {
        if ($key == null) {
            if (isset($_SESSION[$name])) {
                return $_SESSION[$name];
            }
        } else {
            if (isset($_SESSION[$key][$name])) {
                return $_SESSION[$key][$name];
            }
        }
    }

    public static function check($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function delete($key)
    {
        if (self::check($key)) {
            unset($_SESSION[$key]);
            return !self::check($key);
        } else {
            return false;
        }
    }
}
