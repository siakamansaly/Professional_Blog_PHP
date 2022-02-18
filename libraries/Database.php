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
    private $instance = null;

    /**
     * Return connexion database
     * 
     * @return PDO
     */
    public function getPdo(): \PDO
    {     
        if ($this->instance === null) :
            $this->instance = new \PDO($this->env("DB_CONNECTION") . ':dbname=' . $this->env("DB_NAME") . ';charset=' . $this->env("CHARSET") . ';host=' . $this->env("DB_HOST"), $this->env("DB_USER"), $this->env("DB_PASSWORD"));
            $this->instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        endif;
        return $this->instance;
    }

    public function env($param)
    {
       return getenv($param);
    }
    
    
}
