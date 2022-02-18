<?php

namespace Blog\Controllers;

use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    private $comments;
    private $postcategoryModel;
    protected $modelName = \Blog\Models\Post::class;
    public $path;
    public $data;
    public $dataEdit;
    public $errorMessage = "";
    public $post;
    public $slugify;
    protected $auth;
    private $userModel;
    private $categoryModel;


    public function __construct()
    {
        parent::__construct();
        $this->userModel = new \Blog\Models\User;
        $this->comments = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\PostCategory;
        $this->categoryModel = new \Blog\Models\Category;
        $this->slugify = new Slugify();
        $this->auth = new AuthController;
    }


    /**
     * Function add Post
     * 
     * @return void
     */
    public function postAdd()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $categories = "";
        $this->errorMessage = "";
        $check = "";
        //
        if (empty($this->var->files->get('picture'))) {
            $error++;
            $this->errorMessage .= "Taille de fichier trop grande !";
        }

        if (!empty($this->var->files->get('picture'))) {
            $check = $this->checkImage($this->var->files->get('picture'));
            if ($check["success"] === false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }

        $this->data['title'] = $this->sanitize($this->var->request->get('titlePostAdd'));
        $this->data['chapo'] = $this->sanitize($this->var->request->get('chapoPostAdd'));
        $this->data['content'] = $this->sanitize($this->var->request->get('contentPostAdd'));
        $this->data['User_id'] = $this->sanitize($this->var->request->get('authorPostAdd'));
        $this->data['status'] = $this->sanitize($this->var->request->get('statusPostAdd'));
        $categories = json_encode($this->var->request->get('PostCategory_id'));
        $categories = json_decode($categories, true);

        $this->errorMessage = $this->ulAlert($this->errorMessage);

        switch ($error) {
            case 0:
                $this->data['dateAddPost'] = date('Y-m-d H:i:s');
                $this->data['picture'] = $this->uploadImage($this->var->files->get('picture'), __DIR__ . '\..\..\public/img/blog/posts/', $check["extension"]);

                $this->model->insert($this->data);

                $dataPost = [];
                $dataSlug = [];
                $lastid = $this->model->lastInsertIdPDO();
                $dataPost["Post_id"] = $lastid['lastid'];
                $dataSlug['slug'] = $lastid['lastid'] . "-" . $this->slugify->slugify($this->data['title']);

                $this->model->update($lastid['lastid'], $dataSlug);

                foreach ($categories as $categorie) {
                    $dataPost["PostCategory_id"] = $categorie;
                    $this->postcategoryModel->insert($dataPost);
                }
                $message = $this->divAlert("Article ajouté avec succès.", "success");
                $success = true;
                break;

            default:
                $message = $this->divAlert($this->errorMessage, "danger");
                $success = false;
                break;
        }

        $json['success'] = $success;
        $json['message'] = $message;

        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Function update Post
     * 
     * @return void
     */
    public function postEdit()
    {
        $this->dataEdit = [];
        $json = [];
        $success = "";
        $message = "";
        $error = 0;
        $categories = "";
        $this->errorMessage = "";
        $id_post = "";
        $reset = "";
        if ($this->var->files->get('picture') <> "") {
            $check = $this->checkImage($this->var->files->get('picture'));
            if ($check["success"] === false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }

        $id_post = $this->var->request->get('idPostEdit');
        $this->dataEdit['title'] = $this->sanitize($this->var->request->get('titlePostAdd'));
        $this->dataEdit['chapo'] = $this->sanitize($this->var->request->get('chapoPostAdd'));
        $this->dataEdit['content'] = $this->sanitize($this->var->request->get('contentPostAdd'));
        $this->dataEdit['User_id'] = $this->sanitize($this->var->request->get('authorPostAdd'));
        $this->dataEdit['status'] = $this->sanitize($this->var->request->get('statusPostAdd'));
        $this->dataEdit['slug'] = $id_post . "-" . $this->slugify->slugify($this->data['title']);
        $categories = json_encode($this->var->request->get('PostCategory_id'));
        $categories = json_decode($categories, true);

        $this->errorMessage = $this->ulAlert($this->errorMessage);

        switch ($error) {
            case 0:
                $this->dataEdit['dateAddPost'] = date('Y-m-d H:i:s');
                if (!empty($this->var->files->get('picture'))) {
                    $reset = $this->model->read($id_post);
                    if ($reset["picture"] <> "") {
                        $filename = __DIR__ . '/../../public/img/blog/posts/' . $reset['picture'];
                        if (is_file($filename)) {
                            unlink($filename);
                        }
                    }
                    $this->dataEdit['picture'] = $this->uploadImage($this->var->files->get('picture'), __DIR__ . '\..\..\public/img/blog/posts/', $check["extension"]);
                }
                $this->model->update($id_post, $this->dataEdit);

                $dataPost = [];
                $dataPost["Post_id"] = $id_post;
                $this->postcategoryModel->delete($id_post, 'Post_id');
                if (isset($categories)) {
                    foreach ($categories as $categorie) {
                        $dataPost["PostCategory_id"] = $categorie;
                        $this->postcategoryModel->insert($dataPost);
                    }
                }
                $message = $this->divAlert("Article modifié avec succès.", "success");
                $success = true;
                break;

            default:
                $message = $this->divAlert($this->errorMessage, "danger");
                $success = false;
                break;
        }


        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }

    /**
     * Function delete Post
     * 
     * @return void
     */
    public function postDelete()
    {
        $this->data = [];
        $json = [];
        $success = "";
        $message = "";
        $id_post = "";
        $countComments = 0;

        $id_post = $this->var->request->get('idPostDelete');

        $countComments = $this->comments->count('Post_id', $id_post);

        switch ($countComments) {
            case 0:
                // Delete picture post
                $reset = $this->model->read($id_post);
                if ($reset["picture"] <> "") {
                    $filename = __DIR__ . '/../../public/img/blog/posts/' . $reset['picture'];
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                }
                // Delete category of post
                $this->postcategoryModel->delete($id_post, 'Post_id');
                // Delete post
                $this->model->delete($id_post, 'id');
                $message = $this->divAlert("Article supprimé avec succès.", "success");
                break;

            default:
                $this->data['status'] = -1;
                $this->model->update($id_post, $this->data);
                $message = $this->divAlert("L'article a été archivé car il contient des commentaires.", "success");
                break;
        }

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
    public function postManager()
    {
        // Force user login
        $this->auth->forceAdmin();
        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();

        $AllPostsActive = $this->model->count('post.status', '1');
        $AllPostsDisable = $this->model->count('post.status', '0');

        // Pagination 
        $AllPosts = $AllPostsActive + $AllPostsDisable;
        $AllPage = $this->checkAllPage(ceil($AllPosts / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);

        $posts = $this->model->readAllPosts("0,1", "id DESC", "$firstPage,$this->itemsByPage");

        $this->path = '\backend\admin\post\postManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des articles'], 'posts' => $posts, 'users' => $users, 'categories' => $categories, 'AllPostsCounterActive' => $AllPostsActive, 'AllPostsCounterDisable' => $AllPostsDisable, 'AllPage' => $AllPage, 'currentPage' => $currentPage];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show archived post
     * 
     * @return \Twig
     */
    public function postArchived()
    {
        // Force user login
        $this->auth->forceAdmin();

        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();
        $AllPostsArchived = $this->model->count('post.status', '-1');
        $AllPosts = $AllPostsArchived;

        $AllPage = $this->checkAllPage(ceil($AllPosts / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);

        $posts = $this->model->readAllPosts("-1", 'id DESC', "$firstPage,$this->itemsByPage");

        $this->path = '\backend\admin\post\postArchived.html.twig';
        $this->data = ['head' => ['title' => 'Articles archivés'], 'posts' => $posts, 'users' => $users, 'categories' => $categories, 'AllPostsCounterArchived' => $AllPostsArchived, 'AllPage' => $AllPage, 'currentPage' => $currentPage];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show a post Manager
     * 
     * @return \Twig
     */
    public function postManagerEdit($param)
    {
        // Force user login
        $this->auth->forceAdmin();

        $this->path = '\backend\admin\post\postEdit.html.twig';
        $posts = $this->model->readPostById($param);


        if (!$posts) {
            // if no post 
            $this->redirect("/error/404");
        }
        // if post exist
        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();
        $postsCategory = $this->postcategoryModel->readAllCategoriesByPost($posts['id']);
        $postCategory = "";

        foreach ($postsCategory as $value) {
            $postCategory .= $value['id'] . ", ";
        }
        $postCategory = substr($postCategory, 0, -2);

        $this->data = ['head' => ['title' => $posts['title']], 'posts' => $posts, 'users' => $users, 'categories' => $categories, 'postCategory' => $postCategory];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }
}
