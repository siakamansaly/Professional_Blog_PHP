<?php

namespace Blog\Controllers;

use Dotenv\Dotenv;

class Globals 
{
    private $ENV;
    private $dotenv = null;
    
    /**
     * Get all Environment variables
     * @return array
     */
    
    public function env($param = null)
    {
       return getenv($param);
    }
    
    
}
