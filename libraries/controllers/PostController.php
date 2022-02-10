<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Cocur\Slugify\Slugify;


class PostController extends Controller
{
    private $postModel;
    private $comments;
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
        $this->comments = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->categoryModel = new \Blog\Models\PostCategory;
        $this->post = Request::createFromGlobals();
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
        //
        if (empty($this->post->files->all())) {
            $error++;
            $this->errorMessage .= "Taille de fichier trop grande !";
        }

        if (!empty($_FILES)) {
            $check = $this->checkImage($_FILES);
            if ($check["success"] == false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }

        $this->data['title'] = $this->sanitize($this->post->request->get('titlePostAdd'));
        $this->data['chapo'] = $this->sanitize($this->post->request->get('chapoPostAdd'));
        $this->data['content'] = $this->sanitize($this->post->request->get('contentPostAdd'));
        $this->data['User_id'] = $this->sanitize($this->post->request->get('authorPostAdd'));
        $this->data['status'] = $this->sanitize($this->post->request->get('statusPostAdd'));
        $categories = json_encode($this->post->request->get('PostCategory_id'));
        $categories = json_decode($categories, true);

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $this->data['dateAddPost'] = date('Y-m-d H:i:s');
            $this->data['picture'] = $this->uploadImage($_FILES, __DIR__ . '\..\..\public/img/blog/posts/', $check["extension"]);
            $this->postModel->insert($this->data);

            $dataPost = [];
            $dataSlug = [];
            $lastid = $this->postModel->lastInsertIdPDO();
            $dataPost["Post_id"] = $lastid['lastid'];
            $dataSlug['slug'] = $lastid['lastid'] . "-" . $this->slugify->slugify($this->data['title']);

            $this->postModel->update($lastid['lastid'], $dataSlug);

            foreach ($categories as $categorie) {
                $dataPost["PostCategory_id"] = $categorie;
                $this->postcategoryModel->insert($dataPost);
            }
            $message = $this->div_alert("Article ajouté avec succès.", "success");
            $success = true;
        }
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
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

        if ($_FILES['picture']['error'] <> 4) {
            $check = $this->checkImage($_FILES);
            if ($check["success"] == false) {
                $error++;
                $this->errorMessage .= $check["message"];
            }
        }

        $id_post = $this->post->request->get('idPostEdit');
        //print_r($id_post);die;
        $this->data['title'] = $this->sanitize($this->post->request->get('titlePostAdd'));
        $this->data['chapo'] = $this->sanitize($this->post->request->get('chapoPostAdd'));
        $this->data['content'] = $this->sanitize($this->post->request->get('contentPostAdd'));
        $this->data['User_id'] = $this->sanitize($this->post->request->get('authorPostAdd'));
        $this->data['status'] = $this->sanitize($this->post->request->get('statusPostAdd'));
        $this->data['slug'] = $id_post . "-" . $this->slugify->slugify($this->data['title']);
        $categories = json_encode($this->post->request->get('PostCategory_id'));
        $categories = json_decode($categories, true);

        $this->errorMessage = $this->ul_alert($this->errorMessage);

        if ($error > 0) {
            $message = $this->div_alert($this->errorMessage, "danger");
            $success = false;
        } else {
            $this->data['dateAddPost'] = date('Y-m-d H:i:s');
            if ($_FILES['picture']['error'] <> 4 || empty($_FILES)) {
                $this->data['picture'] = $this->uploadImage($_FILES, __DIR__ . '\..\..\public/img/blog/posts/', $check["extension"]);
            }
            $this->postModel->update($id_post, $this->data);

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
        echo json_encode($json);
        exit;
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

        $id_post = $this->post->request->get('idPostDelete');

        $countComments = $this->comments->count('Post_id', $id_post);

        if ($countComments > 0) {
            $this->data['status'] = -1;
            $this->postModel->update($id_post, $this->data);
            $message = $this->div_alert("L'article a été archivé car il contient des commentaires.", "success");
        } else {
            $this->postcategoryModel->delete($id_post, 'Post_id');
            $this->postModel->delete($id_post, 'id');
            $message = $this->div_alert("Article supprimé avec succès.", "success");
        }

        $success = true;
        $json['success'] = $success;
        $json['message'] = $message;
        echo json_encode($json);
        exit;
    }
}
