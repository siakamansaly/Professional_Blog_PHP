<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionController
{
    protected $var;
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Start session PHP
     * @return void
     */
    public function sessionStart()
    {
        if ($this->session->isEmpty()) {
            $this->session->start();
        }
    }
}
