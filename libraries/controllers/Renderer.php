<?php

namespace Blog\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Response;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Blog\Controllers\Globals;
use Blog\Controllers\AuthController;


abstract class Renderer
{
    protected $model;
    protected $modelName;
    protected $twig = null;
    protected $var;
    protected $auth;
    protected $session;
    protected $response;


    public  function __construct()
    {
    }

    /**
     * Start Twig
     * 
     * @return \Twig
     */
    public function render(string $view, array $params = [])
    {

        if ($this->twig == null) {
            $this->twig = new Environment(new FilesystemLoader('./../templates'), [
                'cache' => false, // __DIR__ . '/tmp'
                'needs_context' => true,
            ]);
        }
        $this->auth = new AuthController;
        if ($this->auth->is_login()) {
            $params["logged"] = true;
        }
        if ($this->auth->is_admin()) {
            $params["admin"] = true;
        }
        return $this->twig->display($view, $params);
    }

    /**
     * Redirect http 
     * 
     * @return void
     */
    public function redirect(string $url)
    {
        $response = new RedirectResponse($url);
        return $response->send();
    }


    /**
     * Set HTTP Response 
     * 
     * @return mixed
     */
    public function setResponseHttp(int $code = 200)
    {
        $this->response = new Response();
        $this->response->setStatusCode($code);
        $this->response->send();
    }

    /**
     * Send message PHP Mailer
     * 
     * @return mixed
     */
    public static function sendMessage(array $data): bool
    {
        $ENV = new Globals;
        $mailResponse = true;
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->setLanguage('fr');
        $mail->isSMTP();
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPDebug = 0;
        $mail->Host = $ENV->env("HOST_SMTP");
        $mail->Port = $ENV->env("PORT_SMTP");

        $mail->setFrom($ENV->env("MAIL_FROM"), $ENV->env("MAIL_FIRSTNAME") . " " . $ENV->env("MAIL_LASTNAME"));
        //$mail->addBCC($_ENV['MAIL_FROM'], $_ENV['MAIL_FIRSTNAME'] . " " . $_ENV['MAIL_LASTNAME']);
        $mail->addBCC($data['email'], $data['firstName'] . " " . $data['lastName']);
        $mail->addAddress($ENV->env("MAIL_FROM"), $ENV->env("MAIL_FIRSTNAME") . " " . $ENV->env("MAIL_LASTNAME"));
        $mail->Subject = $data['subject'];
        $mail->Body = <<<EOT
            Email: {$data['email']}
            Expéditeur : {$data['lastName']} {$data['firstName']}
            Message: 
            {$data['message']}
        EOT;

        if (!$mail->send()) : $mailResponse = false;
        endif;

        return $mailResponse;
        $mail->smtpClose();
    }



    public function currentPage(int $AllPage)
    {
        $currentPage = 1;
        if ($this->var->query->get('page') <> "" && $this->var->query->get('page') > 0 && $this->var->query->get('page') <= $AllPage) {
            $currentPage = (int) strip_tags($this->var->query->get('page'));
        }
        return $currentPage;
    }
    public function firstPage(int $currentPage, int $AllPosts, int $AllPostsByPage)
    {
        switch ($AllPosts > $AllPostsByPage) {
            case true:
                $first = ($currentPage * $AllPostsByPage) - $AllPostsByPage;
                break;

            default:
                $first = 0;
                break;
        }
        return $first;
    }

    public function checkAllPage($AllPage)
    {
        if ($AllPage == 0) {
            $AllPage = 1;
        }
        return $AllPage;
    }
}
