<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class PostCategoryController extends Controller
{
    private $postcategoryModel;
    protected $modelName = \Blog\Models\PostCategory::class;
    public $path;
    public $data;
    public $errorMessage = "";
    public $post;
    public $slugify;

    public function __construct()
    {
        parent::__construct();
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
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
   
        $this->data['name'] = $this->sanitize($this->var->request->get('name'));
        $this->data['description'] = $this->sanitize($this->var->request->get('description'));

        $this->model->insert($this->data);
    
        $json['success'] = true;
        $json['message'] = $this->div_alert("Catégorie ajoutée avec succès.", "success");
        $response = new JsonResponse($json);
        $response->send();
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

        $id_category = $this->sanitize($this->var->request->get('idCategoryEdit'));
        $this->data['name'] = $this->sanitize($this->var->request->get('name'));
        $this->data['description'] = $this->sanitize($this->var->request->get('description'));

        $this->model->update($id_category, $this->data);

        $message = $this->div_alert("Catégorie mis à jour avec succès.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();

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

        $id_category = $this->sanitize($this->var->request->get('idCategoryDelete'));

        $this->postcategoryModel->delete($id_category, 'PostCategory_id');

        $this->model->delete($id_category, 'id');

        $message = $this->div_alert("La catégorie a bien été supprimé.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }
}
