<?php
/*
    * PDO Database Class
    * Connect to database
    * Create prepares statements
    * Bind values
    * Return rows and results
*/
namespace Blog;

use Blog\Controllers\Globals;


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
            $ENV = new Globals;
            $ENV = $ENV->allEnv();
            self::$instance = new \PDO($ENV["DB_CONNECTION"] . ':dbname=' . $ENV["DB_NAME"] . ';charset=' . $ENV["CHARSET"] . ';host=' . $ENV["DB_HOST"], $ENV["DB_USER"], $ENV["DB_PASSWORD"]);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        endif;
        return self::$instance;
    }
    
    
}
