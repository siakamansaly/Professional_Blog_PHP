<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class PostCategoryController extends Controller
{
    private $postcategoryModel;
    protected $modelName = \Blog\Models\Category::class;
    public $path;
    public $data;
    public $errorMessage = "";
    public $post;
    public $slugify;
    protected $auth;

    public function __construct()
    {
        parent::__construct();
        $this->postcategoryModel = new \Blog\Models\PostCategory;
        $this->auth = new AuthController;
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
        $json['message'] = $this->divAlert("Catégorie ajoutée avec succès.", "success");
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

        $message = $this->divAlert("Catégorie mis à jour avec succès.", "success");
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

        $message = $this->divAlert("La catégorie a bien été supprimé.", "success");
        $success = true;
    
        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Show post Manager
     * 
     * @return \Twig
     */
    public function categoryManager()
    {
        // Force user login
        $this->auth->forceAdmin();

        $AllCategoryCounter = $this->model->count();

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllCategoryCounter / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);

        $firstPage = $this->firstPage($currentPage, $AllCategoryCounter, $this->itemsByPage);

        $categories = $this->model->readAll("", "id ASC LIMIT $firstPage,$this->itemsByPage");
        $this->path = '\backend\admin\category\categoryManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des catégories'], 'categories' => $categories, 'AllCategoryCounter' => $AllCategoryCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show a comment Manager Edit
     * 
     * @return \Twig
     */
    public function categoryManagerEdit($param)
    {
        // Force user login
        $this->auth->forceAdmin();

        $this->path = '\backend\admin\category\categoryEdit.html.twig';
        $category = $this->model->read($param);


        if (!$category) {
            // if no post 
            $this->redirect("/error/404");
        }
        // if post exist
        $this->data = ['head' => ['title' => "Modifier une catégorie"], 'category' => $category];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }
}
