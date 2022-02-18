<?php

namespace Blog\Controllers;

use Dotenv\Dotenv;

class Globals 
{
    private $ENV;
    private $dotenv;
    
    /**
     * Get all Environment variables
     */
    public function allEnv()
    {   
        $this->dotenv = Dotenv::createImmutable(__DIR__."./../../");
        $this->ENV = $this->dotenv->load();
        return $this->ENV;
    }
    
    
}
