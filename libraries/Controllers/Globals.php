<?php

namespace Blog\Controllers;

class Globals 
{
    /**
     * Get Environment variable
     * @return mixed
     */
    public function env($param = null)
    {
       return getenv($param);
    }
    
    
}
