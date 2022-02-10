<?php
/*
    * PDO Database Class
    * Connect to database
    * Create prepares statements
    * Bind values
    * Return rows and results
*/
namespace Blog;

use Dotenv\Dotenv;
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

class Database
{
    private static $instance = null;
    private $dotenv;
    /**
     * Return connexion database
     * 
     * @return PDO
     */
    public static function getPdo(): \PDO
    {
        if (self::$instance === null) :
            self::$instance = new \PDO($_ENV['DB_CONNECTION'] . ':dbname=' . $_ENV['DB_NAME'] . ';charset=' . $_ENV['CHARSET'] . ';host=' . $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        endif;
        return self::$instance;
    }

    
}
