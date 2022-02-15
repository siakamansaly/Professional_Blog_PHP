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
            if ($_ENV[$name]<>"") {
                return $_ENV[$name];
            }
            return null;
    }    
}
