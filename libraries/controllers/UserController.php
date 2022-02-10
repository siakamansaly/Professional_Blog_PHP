<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


class UserController extends Controller
{

    private $userModel;
    private $commentModel;
    private $postModel;
    protected $modelName = \Models\User::class;
    private $data = [];
    public $var;
    public string $errorMessage;

    public function __construct()
    {
        $this->userModel = new \Blog\Models\User;
        $this->commentModel = new \Blog\Models\Comment;
        $this->postModel = new \Blog\Models\Post;
        $this->var = Request::createFromGlobals();
    }
    /**
     * Function save Profile
     * 
     * @return void
     */
    public function editProfile()
    {
        //print_r($_POST);die;
        if (empty($this->var->request->all())) {
            $this->redirect('/');
        }
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $id = "";
        $this->errorMessage = "";
        $id = $this->sanitize($this->var->request->get('id'));
        $this->data['firstName'] = $this->sanitize($this->var->request->get('firstNameProfile'));
        $this->data['lastName'] = $this->sanitize($this->var->request->get('lastNameProfile'));
        $this->data['email'] = $this->sanitize($this->var->request->get('email'));
        $this->data['phone'] = $this->sanitize($this->var->request->get('phone'));
        $this->data['cvLink'] = $this->sanitize($this->var->request->get('cvLink'));
        $this->data['twitter'] = $this->sanitize($this->var->request->get('twitter'));
        $this->data['gitHub'] = $this->sanitize($this->var->request->get('gitHub'));
        $this->data['linkedIn'] = $this->sanitize($this->var->request->get('linkedIn'));

        if ($this->data['email'] <> SessionController::get('email', 'login')) {
            if ($this->userModel->getEmail($this->data['email']) == 1) {
                $this->errorMessage .= $this->li_alert("L'adresse email renseignée est déja inscrite !");
                $error++;
            }
        }

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $this->userModel->update($id, $this->data);
            $message = $this->div_alert("Sauvegarde effectuée.", "success");

            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }


    /**
     * Function save Picture
     * 
     * @return void
     */
    public function editPicture()
    {
        //print_r($_POST);die;
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $id = "";
        $this->errorMessage = "";
        if (empty($this->var->files->all())) {
            $success = false;
            $json['success'] = $success;
            $json['message'] = "Taille de fichier trop grande !";
            //echo json_encode($json);
            exit;
        }

        $check = $this->checkImage($_FILES);
        if ($check["success"] == false) {
            $error++;
            $this->errorMessage .= $check["message"];
        }
        $id = $this->sanitize($this->var->request->get('id'));

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $userAccount = $this->userModel->read($id);
            $filename = __DIR__ . '/../../public/img/blog/profiles/' . $userAccount['picture'];
            if (file_exists($filename)) {
                unlink($filename);
            }
            $this->data['picture'] = $this->uploadImage($_FILES, __DIR__ . '\..\..\public/img/blog/profiles/', $check["extension"]);

            $this->userModel->update($id, $this->data);
            $message = $this->div_alert("Photo modifiée.", "success");
            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }

    /**
     * Function add Post
     * 
     * @return void
     */
    public function userValidate()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_user = $this->sanitize($this->var->request->get('idUserValidate'));

        $this->data['status'] = 1;
        $this->userModel->update($id_user, $this->data);

        $message = $this->div_alert("Utilisateur validé avec succès.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }

    /**
     * Function delete Post
     * 
     * @return void
     */
    public function userDisable()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_user = $this->sanitize($this->var->request->get('idUserDisable'));

        $this->data['status'] = 0;
        $this->userModel->update($id_user, $this->data);

        $message = $this->div_alert("L'utilisateur a été désactivé avec succès.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }

    /**
     * Function delete Post
     * 
     * @return void
     */
    public function userDelete()
    {
        $this->data = [];
        $commentsUpdate = [];
        $json = [];
        $success = "";
        $message = "";

        $id_user = $this->sanitize($this->var->request->get('idUserDelete'));

        $countPost = $this->postModel->count('User_id', $id_user);

        if ($countPost == 0) {
            
            $commentsUpdate = $this->commentModel->readAllCommentsByUser($id_user, "comment.id");

            // Update child comments user with value 0
            foreach ($commentsUpdate as $comment) {
                $this->commentModel->update($comment['id'], ['parentId' => 0], 'parentId');
            }
            
            // Delete comments user
            $this->commentModel->delete($id_user, 'User_id');
            
            // Delete user
            $this->userModel->delete($id_user, 'id');
            $message = $this->div_alert("L'utilisateur a bien été supprimé.", "success");
            $success = true;
        }
        else
        {
            $message = $this->div_alert("Merci de réattribuer les articles créés par cet utilisateur avant suppression. <br/>Nombre d'article concerné : <b>$countPost</b>", "danger");
            $success = false;
        }

        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }
}
