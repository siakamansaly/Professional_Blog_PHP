<?php
/*
    * PDO Database Class
    * Connect to database
    * Create prepares statements
    * Bind values
    * Return rows and results
*/
namespace Blog;

use Blog\Controllers\EnvironmentController;

class Database
{
    private static $instance = null;
    /**
     * Return connexion database
     * 
     * @return PDO
     */
    public static function getPdo(): \PDO
    {     
        if (self::$instance === null) :
            self::$instance = new \PDO(EnvironmentController::get('DB_CONNECTION') . ':dbname=' . EnvironmentController::get('DB_NAME') . ';charset=' . EnvironmentController::get('CHARSET') . ';host=' . EnvironmentController::get('DB_HOST'), EnvironmentController::get('DB_USER'), EnvironmentController::get('DB_PASSWORD'));
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        endif;
        return self::$instance;
    }
    
    
}
