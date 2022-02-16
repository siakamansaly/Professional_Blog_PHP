<?php

namespace Blog\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;


abstract class Controller
{
    protected $model;
    protected $modelName;
    protected $twig = null;
    public $response;
    protected $var;

    public  function __construct()
    {
        $this->model = new $this->modelName();
        //$this->response = new Response();
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
            $this->twig = new Environment(new FilesystemLoader(BASE_PATH . '/templates'), [
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
     * Show error page
     * 
     * @return \Twig
     */
    public function error(int $code = 404)
    {
        $error = [];
        switch ($code) {
            case 401:
                $error["code"] = 401;
                $error["message"] = "Utilisateur non authentifié.";
                break;
            case 403 :
                $error["code"] = 403;
                $error["message"] = "Accès refusé.";
                break;
            case 405:
                $error["code"] = 405;
                $error["message"] = "Méthode de requête non autorisée.";
                break;
                case 498:
                    $error["code"] = 498;
                    $error["message"] = "Le jeton a expiré ou est invalide.";
                    break;
                
            default:
                $error["code"] = 404;
                $error["message"] = "Cette page n'existe pas ou est invalide.";
                break;
        }

        $path = '\core\404.html.twig';
        $data = ['head' => ['title' => 'Erreur 404'], 'error' => $error];

        $this->setResponseHttp($code);
        $this->render($path, $data);
    }

    /**
     * sanitize data
     * 
     * @return mixed
     */
    public function sanitize($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = strip_tags($data);
        return $data;
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
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->setLanguage('fr');
        $mail->isSMTP();
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPDebug = 0;
        $mail->Host = EnvironmentController::get('HOST_SMTP');
        $mail->Port = EnvironmentController::get('PORT_SMTP');

        $mail->setFrom(EnvironmentController::get('MAIL_FROM'), EnvironmentController::get('MAIL_FIRSTNAME') . " " . EnvironmentController::get('MAIL_LASTNAME'));
        //$mail->addBCC(EnvironmentController::get('MAIL_FROM'), EnvironmentController::get('MAIL_FIRSTNAME') . " " . EnvironmentController::get('MAIL_LASTNAME'));
        $mail->addBCC($data['email'], $data['firstName'] . " " . $data['lastName']);
        $mail->addAddress(EnvironmentController::get('MAIL_FROM'), EnvironmentController::get('MAIL_FIRSTNAME') . " " . EnvironmentController::get('MAIL_LASTNAME'));
        $mail->Subject = $data['subject'];
        $mail->Body = <<<EOT
            Email: {$data['email']}
            Expéditeur : {$data['lastName']} {$data['firstName']}
            Message: 
            {$data['message']}
        EOT;

        if (!$mail->send()) {
            return false;
            $mail->smtpClose();
        } else {
            return true;
            $mail->smtpClose();
        }
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

        $name = $file->getClientOriginalName();
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
