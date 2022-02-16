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
    public $errorMessage = "";
    public $post;
    public $slugify;

    public function __construct()
    {
        parent::__construct();
        $this->comments = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->slugify = new Slugify();
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

        //print_r($this->var->files->get('picture'));die;

        if (!empty($this->var->files->get('picture'))) {
            $check = $this->checkImage($this->var->files->get('picture'));
            if ($check["success"] == false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }
        //print_r($check);die;

        $this->data['title'] = $this->sanitize($this->var->request->get('titlePostAdd'));
        $this->data['chapo'] = $this->sanitize($this->var->request->get('chapoPostAdd'));
        $this->data['content'] = $this->sanitize($this->var->request->get('contentPostAdd'));
        $this->data['User_id'] = $this->sanitize($this->var->request->get('authorPostAdd'));
        $this->data['status'] = $this->sanitize($this->var->request->get('statusPostAdd'));
        $categories = json_encode($this->var->request->get('PostCategory_id'));
        $categories = json_decode($categories, true);

        $this->errorMessage = $this->ul_alert($this->errorMessage);

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
                $message = $this->div_alert("Article ajouté avec succès.", "success");
                $success = true;
                break;

            default:
                $message = $this->div_alert($this->errorMessage, "danger");
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
        $this->data = [];
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
            if ($check["success"] == false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }

        $id_post = $this->var->request->get('idPostEdit');
        //print_r($id_post);die;
        $this->data['title'] = $this->sanitize($this->var->request->get('titlePostAdd'));
        $this->data['chapo'] = $this->sanitize($this->var->request->get('chapoPostAdd'));
        $this->data['content'] = $this->sanitize($this->var->request->get('contentPostAdd'));
        $this->data['User_id'] = $this->sanitize($this->var->request->get('authorPostAdd'));
        $this->data['status'] = $this->sanitize($this->var->request->get('statusPostAdd'));
        $this->data['slug'] = $id_post . "-" . $this->slugify->slugify($this->data['title']);
        $categories = json_encode($this->var->request->get('PostCategory_id'));
        $categories = json_decode($categories, true);

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $this->data['dateAddPost'] = date('Y-m-d H:i:s');
            if (!empty($this->var->files->get('picture'))) {
                $reset = $this->model->read($id_post);
                if ($reset["picture"] <> "") {
                    $filename = __DIR__ . '/../../public/img/blog/posts/' . $reset['picture'];
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                }
                $this->data['picture'] = $this->uploadImage($this->var->files->get('picture'), __DIR__ . '\..\..\public/img/blog/posts/', $check["extension"]);
            }
            $this->model->update($id_post, $this->data);

            $dataPost = [];
            $dataPost["Post_id"] = $id_post;
            $this->postcategoryModel->delete($id_post, 'Post_id');
            if (isset($categories)) {
                foreach ($categories as $categorie) {
                    $dataPost["PostCategory_id"] = $categorie;
                    $this->postcategoryModel->insert($dataPost);
                }
            }
            $message = $this->div_alert("Article modifié avec succès.", "success");
            $success = true;
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

        if ($countComments > 0) {
            $this->data['status'] = -1;
            $this->model->update($id_post, $this->data);
            $message = $this->div_alert("L'article a été archivé car il contient des commentaires.", "success");
        } else {
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
            $message = $this->div_alert("Article supprimé avec succès.", "success");
        }

        $success = true;
        $json['success'] = $success;
        $json['message'] = $message;
        $response = new JsonResponse($json);
        $response->send();
    }
}
