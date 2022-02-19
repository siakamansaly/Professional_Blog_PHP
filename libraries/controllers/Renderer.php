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

    /**
     * Start Twig
     * @return \Twig
     */
    public function render(string $view, array $params = [])
    {

        if ($this->twig === null) {
            $this->twig = new Environment(new FilesystemLoader('./../templates'), [
                'cache' => false, // __DIR__ . '/tmp'
                'needs_context' => true,
            ]);
        }
        $this->auth = new AuthController;
        if ($this->auth->isLogin()) {
            $params["logged"] = true;
        }
        if ($this->auth->isAdmin()) {
            $params["admin"] = true;
        }
        return $this->twig->display($view, $params);
    }

    /**
     * Redirect http 
     * @return mixed
     */
    public function redirect(string $url)
    {
        $response = new RedirectResponse($url);
        return $response->send();
    }


    /**
     * Set HTTP Response 
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
     * @return bool
     */
    public static function sendMessage(array $data): bool
    {
        $ENV = new Globals;
        $mailResponse = true;
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->setLanguage('fr');
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $ENV->env("HOST_SMTP");
        $mail->Port = $ENV->env("PORT_SMTP");

        $mail->setFrom($ENV->env("MAIL_FROM"), $ENV->env("MAIL_FIRSTNAME") . " " . $ENV->env("MAIL_LASTNAME"));
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


    /**
     * Select current page
     * @return int
     */
    public function currentPage(int $AllPage) : int
    {
        $currentPage = 1;
        if ($this->var->query->get('page') <> "" && $this->var->query->get('page') > 0 && $this->var->query->get('page') <= $AllPage) {
            $currentPage = (int) strip_tags($this->var->query->get('page'));
        }
        return $currentPage;
    }

    /**
     * Select first page
     * @return int
     */
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

    /**
     * Check page
     * @return int
     */
    public function checkAllPage($AllPage)
    {
        if ($AllPage == 0) {
            $AllPage = 1;
        }
        return $AllPage;
    }
}
