<?php

namespace Blog\Controllers;

class Globals 
{
    /**
     * Get all Environment variables
     * @return array
     */
    
    public function env($param = null)
    {
       return getenv($param);
    }
    
    
}
