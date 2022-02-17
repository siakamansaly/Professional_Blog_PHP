<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionController extends Controller
{
    protected $var;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Start session PHP
     * @return void
     */
    public static function sessionStart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function hasSession() : bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            return true;
        }
        return false;
    }

    /**
     * Set a session variable with 1 or 2 parameters
     * @return bool
     */
    public static function set(string $name, $value, ?string $key = null): bool
    {
        switch ($key) {
            case null:
                $_SESSION[$name] = $value;
                break;
            default:
                $_SESSION[$key][$name] = $value;
                break;
        }
        return true;
    }

    /**
     * Get a session variable with 1 or 2 parameters
     * @return mixed
     */
    public static function get(string $name, ?string $key = null)
    {
        if ($key) {
            if (isset($_SESSION[$key][$name])) {
                return $_SESSION[$key][$name];
            }
        } else {
            if (isset($_SESSION[$name])) {
                return $_SESSION[$name];
            }
        }
    }

    /**
     * Check if a session variable exist
     * @return bool
     */
    public static function check($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Delete a session variable
     * @return bool
     */
    public static function delete($key): bool
    {
        if (self::check($key)) {
            unset($_SESSION[$key]);
            return true;
        } else {
            return false;
        }
    }
}
