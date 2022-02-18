<?php

namespace Blog\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Blog\Controllers\Globals;


abstract class Controller
{
    protected $model;
    protected $modelName;
    protected $twig = null;
    protected $var;

    public  function __construct()
    {
        $this->model = new $this->modelName();
        $this->var = Request::createFromGlobals();
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
        
        if (AuthController::is_login()) {
            $params["logged"] = true;
        }
        if (AuthController::is_admin()) {
            $params["admin"] = true;
        }
        return $this->twig->display($view, $params);
    }

    
    /**
     * sanitize data
     * 
     * @return mixed
     */
    public function sanitize($data)
    {
        $data = trim($data);
        $data = htmlspecialchars($data);
        $data = strip_tags($data);
        return $data;
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
     * Return Message with balise 'div'
     * @param string Message
     * @param string [success|danger]
     * @return mixed
     */
    public function div_alert(string $message, string $alert = "success")
    {
        return '<div class="col mb-4 justify-content-center alert alert-' . $alert . '">' . $message . '</div>';
    }

    /**
     * Return Message with balise 'li'
     * @return mixed
     */
    public function li_alert(string $message)
    {
        return "<li class='list-unstyled'><i class='fas fa-exclamation-triangle mx-2'></i>  " . $message . "</li>";
    }

    /**
     * Return Message with balise 'ul'
     * @param string
     * @return mixed
     */
    public function ul_alert(string $message)
    {
        return "<ul>" . $message . "</ul>";
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

        if (!$mail->send()) : $mailResponse=false; endif;

        return $mailResponse;
        $mail->smtpClose();

    }

    /**
     * Check uploaded image
     * 
     * @return array ["success" => true or false, "message"=> string]
     */
    public function checkImage(UploadedFile $file): array
    {
        $result = [];
        $errorMessage = "";
        $error = 0;
        $maxSize = 5242880;

        //$name = $file->getClientOriginalName();
        $size = $file->getSize();
        $errorFile = $file->getError();

        $extension = $file->getClientOriginalExtension();

        //Tableau des extensions que l'on accepte
        $extensions = ['jpg', 'png', 'jpeg'];
        if (!in_array($extension, $extensions)) {
            $errorMessage .= $this->li_alert("Extension de fichier non acceptée ! Extensions autorisées : png, jpg, jpeg");
            $error++;
        }
        if ($errorFile <> 0) {
            $errorMessage .= $this->li_alert("Impossible de charger ce fichier ! Erreur No. $errorFile");
            $error++;
        }
        if ($size > $maxSize) {
            $errorMessage .= $this->li_alert("Taille de fichier trop grande !");
            $error++;
        }

        if ($error > 0) {
            $result['success'] = false;
        } else {
            $result['success'] = true;
        }
        $result['message'] = $errorMessage;
        $result['extension'] = $extension;
        $result['error'] = $errorFile;

        return $result;
    }

    /**
     * Upload image in a directory
     * @return string
     */
    public function uploadImage(UploadedFile $file, string $fileDir, string $extension)
    {
        if ($file) {
            $uniqid = uniqid('', false);
            $uniqname = $uniqid . '.' . $extension;
            $filePath = "{$fileDir}/";
            $file->move($filePath, $uniqname);
            unset($file);
            return $uniqname;
        }
    }

    public function currentPage(int $AllPage)
    {
        if ($this->var->query->get('page') <> "" && $this->var->query->get('page') > 0 && $this->var->query->get('page') <= $AllPage) {
            $currentPage = (int) strip_tags($this->var->query->get('page'));
        } else {
            $currentPage = 1;
        }
        return $currentPage;
    }
    public function firstPage(int $currentPage, int $AllPosts, int $AllPostsByPage)
    {
        if ($AllPosts > $AllPostsByPage) {
            $first = ($currentPage * $AllPostsByPage) - $AllPostsByPage;
        } else {
            $first = 0;
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
