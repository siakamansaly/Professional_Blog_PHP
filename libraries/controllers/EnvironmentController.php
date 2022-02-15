<?php

namespace Blog\Controllers;

class EnvironmentController
{
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
