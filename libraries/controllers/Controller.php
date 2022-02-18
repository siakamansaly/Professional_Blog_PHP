<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;
use Blog\Controllers\Renderer;


abstract class Controller extends Renderer
{
    protected $model;
    protected $modelName;
    protected $twig = null;
    protected $var;
    protected $auth;
    protected $session;
    protected $itemsByPage = 9;


    public  function __construct()
    {
        $this->model = new $this->modelName();
        $this->var = Request::createFromGlobals();
        $this->session = new Session();

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
     * Return Message with balise 'div'
     * @param string Message
     * @param string [success|danger]
     * @return mixed
     */
    public function divAlert(string $message, string $alert = "success")
    {
        return '<div class="col mb-4 justify-content-center alert alert-' . $alert . '">' . $message . '</div>';
    }

    /**
     * Return Message with balise 'li'
     * @return mixed
     */
    public function liAlert(string $message)
    {
        return "<li class='list-unstyled'><i class='fas fa-exclamation-triangle mx-2'></i>  " . $message . "</li>";
    }

    /**
     * Return Message with balise 'ul'
     * @param string
     * @return mixed
     */
    public function ulAlert(string $message)
    {
        return "<ul>" . $message . "</ul>";
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

        $size = $file->getSize();
        $errorFile = $file->getError();

        $extension = $file->getClientOriginalExtension();

        //Tableau des extensions que l'on accepte
        $extensions = ['jpg', 'png', 'jpeg'];
        if (!in_array($extension, $extensions)) {
            $errorMessage .= $this->liAlert("Extension de fichier non acceptée ! Extensions autorisées : png, jpg, jpeg");
            $error++;
        }
        if ($errorFile <> 0) {
            $errorMessage .= $this->liAlert("Impossible de charger ce fichier ! Erreur No. $errorFile");
            $error++;
        }
        if ($size > $maxSize) {
            $errorMessage .= $this->liAlert("Taille de fichier trop grande !");
            $error++;
        }

        switch ($error) {
            case 0:
                $result['success'] = true;
                break;

            default:
                $result['success'] = false;
                break;
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


}
