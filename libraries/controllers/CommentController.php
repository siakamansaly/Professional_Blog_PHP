<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CommentController extends Controller
{
    private $postModel;
    private $commentModel;
    private $postcategoryModel;
    private $categoryModel;
    protected $modelName = \Blog\Models\Comment::class;
    public $path;
    public $data;
    public $errorMessage = "";
    public $post;
    public $slugify;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new \Blog\Models\Post;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->categoryModel = new \Blog\Models\PostCategory;
    }


    

    /**
     * Function add Post
     * 
     * @return void
     */
    public function commentAdd()
    {
        $this->data = [];
        $json = [];
   
        $this->data['parentId'] = $this->sanitize($this->var->request->get('parent_id'));
        $this->data['Post_id'] = $this->sanitize($this->var->request->get('post_id'));
        $this->data['dateAddComment'] = date('Y-m-d H:i:s');
        $this->data['User_id'] = SessionController::get('id', 'login');
        $this->data['content'] = $this->sanitize($this->var->request->get('comment'));

        if (SessionController::get('userType', 'login')=="admin")
        {
            $this->data['status'] = 1;
        }
        else
        {
            $this->data['status'] = 0;
        }

        $this->model->insert($this->data);
    
        $json['success'] = true;
        $json['message'] = $this->div_alert("Commentaire ajouté avec succès et en attente de modération par l'administrateur.", "success");
        print_r(json_encode($json));

    }

    
    /**
     * Function delete Post
     * 
     * @return void
     */
    public function commentEdit()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $id_comment ="";

        $id_comment = $this->sanitize($this->var->request->get('idCommentEdit'));
        $this->data['content'] = $this->sanitize($this->var->request->get('comment'));
        $this->data['status'] = $this->sanitize($this->var->request->get('status'));

        $this->model->update($id_comment, $this->data);

        $message = $this->div_alert("Commentaire mis à jour avec succès.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        $json['status'] = $this->data['status'];
        print_r(json_encode($json));

    }

    /**
     * Function delete Post
     * 
     * @return void
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

        $message = $this->div_alert("Commentaire validé avec succès.", "success");
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
    public function commentDisable()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_comment = $this->sanitize($this->var->request->get('idCommentDisable'));

        $this->data['status'] = 2;
        $this->model->update($id_comment, $this->data);

        $message = $this->div_alert("Le commentaire a bien été désapprouvé.", "success");
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
    public function commentDelete()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        
        $id_comment = $this->sanitize($this->var->request->get('idCommentDelete'));

        $this->model->update($id_comment, ['parentId' => 0], 'parentId');
        $this->model->delete($id_comment, 'id');

        $message = $this->div_alert("Le commentaire a bien été supprimé.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        print_r(json_encode($json));
    }
}
