<?php

namespace Blog\Controllers;

class EnvironmentController
{
    public $dotenv;

    public function __construct()
    {
        $this->dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $this->dotenv->load();
    }

    /**
     * Get a Environment variable
     * @return mixed
     */
    public static function get(string $name)
    {
            if (getenv($name)<>"") {
                return getenv($name);
            }
            return null;
    }

   
    
}
