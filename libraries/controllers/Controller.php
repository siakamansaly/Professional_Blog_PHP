<?php

namespace Blog\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Blog\Controllers\PhpAdditionalExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPMailer\PHPMailer\PHPMailer;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

abstract class Controller
{
    protected $model;
    protected $modelName;
    protected static $twig = null;
    public $request;
    public $response;
    private $var;

    public  function __construct()
    {
        $this->model = new $this->modelName();
        $this->request = Request::createFromGlobals();
        $this->response = new Response();
        $this->var = Request::createFromGlobals();
    }


    /**
     * Start Twig
     * 
     * @return \Twig
     */
    public static function render(string $view, array $params = [])
    {
        if (self::$twig == null) {
            self::$twig = new Environment(new FilesystemLoader(BASE_PATH . '/templates'), [
                'cache' => false, // __DIR__ . '/tmp'
                'needs_context' => true,
            ]);
            self::$twig->addGlobal('session', filter_var_array($_SESSION));
            self::$twig->addExtension(new PhpAdditionalExtension());
        }
        return self::$twig->display($view, $params);
    }

    /**
     * Show error page
     * 
     * @return \Twig
     */
    public function error(array $error = [])
    {
        //var_dump($error);die;
        $path = '\core\404.html.twig';
        $data = ['head' => ['title' => 'Erreur 404'], 'error' => $error];
        $this->setResponseHttp(404);
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
     * Redirect http 
     * 
     * @return void
     */
    public function redirect(string $url, $param = null)
    {
        header('Location: ' . $url . $param);
        exit;
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
        $mail->Host = $_ENV['HOST_SMTP'];
        $mail->Port = $_ENV['PORT_SMTP'];

        $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FIRSTNAME'] . " " . $_ENV['MAIL_LASTNAME']);
        //$mail->addBCC($_ENV['MAIL_FROM'], $_ENV['MAIL_FIRSTNAME'] . " " . $_ENV['MAIL_LASTNAME']);
        $mail->addBCC($data['email'], $data['firstName'] . " " . $data['lastName']);
        $mail->addAddress($_ENV['MAIL_FROM'], $_ENV['MAIL_FIRSTNAME'] . " " . $_ENV['MAIL_LASTNAME']);
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
     * @param array $file
     * @return array ["success" => true or false, "message"=> string]
     */
    public function checkImage(array $file): array
    {
        $result = [];
        $errorMessage = "";
        $error = 0;
        $maxSize = 5242880;

        $tmpName = $file['picture']['tmp_name'];
        $name = $file['picture']['name'];
        $size = $file['picture']['size'];
        $errorFile = $file['picture']['error'];


        $tabExtension = explode('.', $name);
        $extension = strtolower(end($tabExtension));

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
     * @param array $file
     * @param string $fileDir
     * @param string $extension
     * @return string
     */
    public function uploadImage(array $file, string $fileDir, string $extension)
    {
        if (!empty($file['picture']['tmp_name'])) {
            $uniqid = uniqid('', false);
            $uniqname = $uniqid . '.' . $extension;
            $filePath = "{$fileDir}/{$uniqname}";
            move_uploaded_file($file['picture']['tmp_name'], $filePath);
            return $uniqname;
        }
    }

    public function currentPage(int $AllPage)
    {
        if (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $AllPage) {
            $currentPage = (int) strip_tags($_GET['page']);
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
            $AllPage=1; 
        }
        return $AllPage;
    }

    
}
