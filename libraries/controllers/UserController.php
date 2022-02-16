<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{
    private $commentModel;
    private $postModel;
    protected $modelName = \Blog\Models\User::class;
    private $data = [];
    public string $errorMessage;

    public function __construct()
    {
        parent::__construct();
        $this->commentModel = new \Blog\Models\Comment;
        $this->postModel = new \Blog\Models\Post;
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
            $this->error(405);
        }
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $id = "";
        $this->errorMessage = "";
        $emailOld = "";

        $id = (int) $this->sanitize($this->var->request->get('id'));
        $emailOld = $this->model->read($id);
        $this->data['firstName'] = $this->sanitize($this->var->request->get('firstNameProfile'));
        $this->data['lastName'] = $this->sanitize($this->var->request->get('lastNameProfile'));
        $this->data['email'] = $this->sanitize($this->var->request->get('email'));
        $this->data['phone'] = $this->sanitize($this->var->request->get('phone'));
        $this->data['cvLink'] = $this->sanitize($this->var->request->get('cvLink'));
        $this->data['twitter'] = $this->sanitize($this->var->request->get('twitter'));
        $this->data['gitHub'] = $this->sanitize($this->var->request->get('gitHub'));
        $this->data['linkedIn'] = $this->sanitize($this->var->request->get('linkedIn'));
        if ($this->var->request->get('userType') <> "") {
            $this->data['userType'] = $this->sanitize($this->var->request->get('userType'));
        }

        if ($this->data['email'] <> $emailOld['email']) {
            if ($this->model->getEmail($this->data['email']) == 1) {
                $this->errorMessage .= $this->li_alert("L'adresse email renseignée est déja inscrite ! ");
                $error++;
            }
        }

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $this->model->update($id, $this->data);
            $message = $this->div_alert("Sauvegarde effectuée.", "success");

            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
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
        if (empty($this->var->files->get('picture'))) {
            $success = false;
            $json['success'] = $success;
            $json['message'] = "Une erreur est survenue au niveau de l'image ...";
        } else {
            $check = $this->checkImage($this->var->files->get('picture'));
            if ($check["success"] == false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }

        $id = $this->sanitize($this->var->request->get('id'));

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $userAccount = $this->model->read($id);
            if ($userAccount['picture'] <> "") {
                $filename = __DIR__ . '/../../public/img/blog/profiles/' . $userAccount['picture'];
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
            $this->data['picture'] = $this->uploadImage($this->var->files->get('picture'), __DIR__ . '\..\..\public/img/blog/profiles/', $check["extension"]);

            $this->model->update($id, $this->data);
            $message = $this->div_alert("Photo modifiée.", "success");
            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
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
        $this->model->update($id_user, $this->data);

        $message = $this->div_alert("Utilisateur validé avec succès.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
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
        $this->model->update($id_user, $this->data);

        $message = $this->div_alert("L'utilisateur a été désactivé avec succès.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
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
            $this->model->delete($id_user, 'id');
            $message = $this->div_alert("L'utilisateur a bien été supprimé.", "success");
            $success = true;
        } else {
            $message = $this->div_alert("Merci de réattribuer les articles créés par cet utilisateur avant suppression. <br/>Nombre d'article concerné : <b>$countPost</b>", "danger");
            $success = false;
        }

        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
    }
}
