<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;


class CommentController extends Controller
{
    protected $modelName = \Blog\Models\Comment::class;
    private $path;
    private $data;
    protected $auth;
    private $userModel;
    private $commentModel;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthController;
        $this->userModel = new \Blog\Models\User;
        $this->commentModel = new \Blog\Models\Comment;
    }

    /**
     * Function add comment
     * @return json
     */
    public function commentAdd()
    {
        $this->data = [];
        $json = [];

        $this->data['parentId'] = $this->sanitize($this->var->request->get('parent_id'));
        $this->data['Post_id'] = $this->sanitize($this->var->request->get('post_id'));
        $this->data['dateAddComment'] = date('Y-m-d H:i:s');
        $this->data['User_id'] = $this->session->get('id');
        $this->data['content'] = $this->sanitize($this->var->request->get('comment'));
        $this->data['status'] = 0;

        if ($this->session->get('userType') == "admin") {
            $this->data['status'] = 1;
        }


        $this->model->insert($this->data);

        if ($this->data['status'] === 0) {
            $user = $this->userModel->read($this->data['User_id']);
            $ENV = new Globals;
            $titleWebSite = $ENV->env("TITLE_WEBSITE");
            $this->data['subject'] = $titleWebSite . " - Nouveau commentaire";
            $this->data['message'] = "Votre commentaire a bien été soumis.\nCelui-ci va être examiné par l'administrateur avant validation.\nBien à vous";
            $this->data['email'] = $user['email'];
            $this->data['firstName'] = $user['firstName'];
            $this->data['lastName'] = $user['lastName'];
            $this->sendMessage($this->data);
        }

        $json['success'] = true;
        $json['message'] = $this->divAlert("Commentaire ajouté avec succès et en attente de modération par l'administrateur.", "success");
        $response = new JsonResponse($json);
        $response->send();
    }


    /**
     * Function edit comment
     * @return json
     */
    public function commentEdit()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $id_comment = "";

        $id_comment = $this->sanitize($this->var->request->get('idCommentEdit'));
        $this->data['content'] = $this->sanitize($this->var->request->get('comment'));
        $this->data['status'] = $this->sanitize($this->var->request->get('status'));

        $this->model->update($id_comment, $this->data);

        $message = $this->divAlert("Commentaire mis à jour avec succès.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        $json['status'] = $this->data['status'];
        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Function validate comment
     * @return json
     */
    public function commentValidate()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_comment = $this->sanitize($this->var->request->get('idCommentValidate'));

        $this->data['status'] = 1;
        $this->model->update($id_comment, $this->data);

        $comment = $this->commentModel->read($id_comment);
        $user = $this->userModel->read($comment['User_id']);
        $ENV = new Globals;
        $titleWebSite = $ENV->env("TITLE_WEBSITE");
        $this->data['subject'] = $titleWebSite . " - Commentaire approuvé";
        $this->data['message'] = "Votre commentaire a bien été approuvé.\nBien à vous";
        $this->data['email'] = $user['email'];
        $this->data['firstName'] = $user['firstName'];
        $this->data['lastName'] = $user['lastName'];
        $this->sendMessage($this->data);

        $message = $this->divAlert("Commentaire validé avec succès.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Function disable comment
     * @return json
     */
    public function commentDisable()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_comment = $this->sanitize($this->var->request->get('idCommentDisable'));

        $this->data['status'] = 2;
        $this->model->update($id_comment, $this->data);

        $comment = $this->commentModel->read($id_comment);
        $user = $this->userModel->read($comment['User_id']);
        $ENV = new Globals;
        $titleWebSite = $ENV->env("TITLE_WEBSITE");
        $this->data['subject'] = $titleWebSite . " - Commentaire désapprouvé";
        $this->data['message'] = "Votre commentaire a été désapprouvé car il ne répond pas à nos conditions générales d'utilisation.\nBien à vous";
        $this->data['email'] = $user['email'];
        $this->data['firstName'] = $user['firstName'];
        $this->data['lastName'] = $user['lastName'];
        $this->sendMessage($this->data);

        $message = $this->divAlert("Le commentaire a bien été désapprouvé.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Function delete comment
     * @return json
     */
    public function commentDelete()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_comment = $this->sanitize($this->var->request->get('idCommentDelete'));

        $this->model->update($id_comment, ['parentId' => 0], 'parentId');
        $this->model->delete($id_comment, 'id');

        $message = $this->divAlert("Le commentaire a bien été supprimé.", "success");
        $success = true;

        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Show comment Manager
     * @return \Twig
     */
    public function commentManager()
    {
        // Force user login
        $this->auth->forceAdmin();
        $status = 0;

        if ($this->var->query->get('status')) {
            $status = $this->sanitize($this->var->query->get('status'));
        }

        $AllCommentCounter = $this->model->count("comment.status", "$status");

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllCommentCounter / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);

        $firstPage = $this->firstPage($currentPage, $AllCommentCounter, $this->itemsByPage);

        $comments = $this->model->readAllCommentsByStatus("$status", "id DESC", "$firstPage,$this->itemsByPage");
        $this->path = '\backend\admin\comment\commentManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des commentaires'], 'comments' => $comments, 'AllCommentCounter' => $AllCommentCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'status' => $status];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show a comment Manager Edit
     * @return \Twig
     */
    public function commentManagerEdit($param)
    {
        // Force user login
        $this->auth->forceAdmin();

        $this->path = '\backend\admin\comment\commentEdit.html.twig';
        $comments = $this->model->readCommentById($param);


        // if no post exist
        if (!$comments) {
            $this->redirect("/error/404");
        }

        $this->data = ['head' => ['title' => "Modifier un commentaire"], 'comments' => $comments];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }
}
