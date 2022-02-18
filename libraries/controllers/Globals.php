<?php

namespace Blog\Controllers;

use Dotenv\Dotenv;

class Globals 
{
    private static $ENV;
    private static $dotenv;
    
    /**
     * Get all Environment variables
     */
    public static function allEnv()
    {   
        self::$dotenv = Dotenv::createImmutable(__DIR__."./../../");
        self::$ENV = self::$dotenv->load();
        return self::$ENV;
    }
    
    
}
