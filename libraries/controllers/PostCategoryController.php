<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;


class PostCategoryController extends Controller
{
    private $postModel;
    private $commentsModel;
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
        $this->commentsModel = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->categoryModel = new \Blog\Models\PostCategory;
        $this->post = Request::createFromGlobals();
    }


    

    /**
     * Function add category
     * 
     * @return void
     */
    public function categoryAdd()
    {
        $this->data = [];
        $json = [];
   
        $this->data['name'] = $this->sanitize($this->post->request->get('name'));
        $this->data['description'] = $this->sanitize($this->post->request->get('description'));

        $this->categoryModel->insert($this->data);
    
        $json['success'] = true;
        $json['message'] = $this->div_alert("Catégorie ajoutée avec succès.", "success");
        echo json_encode($json);
        exit;
    }

    
    /**
     * Function edit category
     * 
     * @return void
     */
    public function categoryEdit()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $id_category ="";

        $id_category = $this->sanitize($this->post->request->get('idCategoryEdit'));
        $this->data['name'] = $this->sanitize($this->post->request->get('name'));
        $this->data['description'] = $this->sanitize($this->post->request->get('description'));

        $this->categoryModel->update($id_category, $this->data);

        $message = $this->div_alert("Catégorie mis à jour avec succès.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }


    /**
     * Function delete category
     * 
     * @return void
     */
    public function categoryDelete()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";

        $id_category = $this->sanitize($this->post->request->get('idCategoryDelete'));

        $this->postcategoryModel->delete($id_category, 'PostCategory_id');

        $this->categoryModel->delete($id_category, 'id');

        $message = $this->div_alert("La catégorie a bien été supprimé.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }
}
