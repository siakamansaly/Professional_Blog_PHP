<?php
/*
    * PDO Database Class
    * Connect to database
*/
namespace Blog;

use Blog\Controllers\Globals;


class Database
{
    private $instance = null;

    /**
     * Return connexion database
     * 
     * @return PDO
     */
    public function getPdo(): \PDO
    {     
        if ($this->instance === null) :
            $ENV = new Globals;
            $db_user = (string) $ENV->env("DB_USER");
            $db_password = (string) $ENV->env("DB_PASSWORD");

            $this->instance = new \PDO($ENV->env("DB_CONNECTION") . ':dbname=' . $ENV->env("DB_NAME") . ';charset=' . $ENV->env("CHARSET") . ';host=' . $ENV->env("DB_HOST"), $db_user, $db_password);
            $this->instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        endif;
        return $this->instance;
    }
    
    
}
