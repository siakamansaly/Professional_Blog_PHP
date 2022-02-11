<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;


class CommentController extends Controller
{
    private $postModel;
    private $commentModel;
    private $postcategoryModel;
    private $categoryModel;
    protected $modelName = \Models\Post::class;
    public $path;
    public $data;
    public $errorMessage = "";
    public $post;
    public $slugify;

    public function __construct()
    {
        $this->postModel = new \Blog\Models\Post;
        $this->commentModel = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->categoryModel = new \Blog\Models\PostCategory;
        $this->post = Request::createFromGlobals();
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
   
        $this->data['parentId'] = $this->sanitize($this->post->request->get('parent_id'));
        $this->data['Post_id'] = $this->sanitize($this->post->request->get('post_id'));
        $this->data['dateAddComment'] = date('Y-m-d H:i:s');
        $this->data['User_id'] = SessionController::get('id', 'login');
        $this->data['content'] = $this->sanitize($this->post->request->get('comment'));

        if (SessionController::get('userType', 'login')=="admin")
        {
            $this->data['status'] = 1;
        }
        else
        {
            $this->data['status'] = 0;
        }

        $this->commentModel->insert($this->data);
    
        $json['success'] = true;
        $json['message'] = $this->div_alert("Commentaire ajouté avec succès et en attente de modération par l'administrateur.", "success");
        echo json_encode($json);
        exit;
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

        $id_comment = $this->sanitize($this->post->request->get('idCommentEdit'));
        $this->data['content'] = $this->sanitize($this->post->request->get('comment'));
        $this->data['status'] = $this->sanitize($this->post->request->get('status'));

        $this->commentModel->update($id_comment, $this->data);

        $message = $this->div_alert("Commentaire mis à jour avec succès.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        $json['status'] = $this->data['status'];
        echo json_encode($json);
        exit;
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

        $id_comment = $this->sanitize($this->post->request->get('idCommentValidate'));

        $this->data['status'] = 1;
        $this->commentModel->update($id_comment, $this->data);

        $message = $this->div_alert("Commentaire validé avec succès.", "success");
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
    public function commentDisable()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_comment = $this->sanitize($this->post->request->get('idCommentDisable'));

        $this->data['status'] = 2;
        $this->commentModel->update($id_comment, $this->data);

        $message = $this->div_alert("Le commentaire a bien été désapprouvé.", "success");
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
    public function commentDelete()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        
        $id_comment = $this->sanitize($this->post->request->get('idCommentDelete'));

        $this->commentModel->update($id_comment, ['parentId' => 0], 'parentId');
        $this->commentModel->delete($id_comment, 'id');

        $message = $this->div_alert("Le commentaire a bien été supprimé.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }
}
