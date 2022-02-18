<?php

namespace Blog\Controllers;

use Symfony\Component\HttpFoundation\Request;

class BackEndController extends Controller
{
    protected $modelName = \Blog\Models\Contact::class;
    private $path;
    private $data;
    private $userModel;
    private $postModel;
    private $categoryModel;
    private $commentsModel;
    private $postcategoryModel;
    private $itemsByPage = 9;
    protected $auth;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new \Blog\Models\User;
        $this->postModel = new \Blog\Models\Post;
        $this->categoryModel = new \Blog\Models\PostCategory;
        $this->commentsModel = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->auth = new AuthController;
    }

    /**
     * Show index page
     * 
     * @return \Twig
     */
    public function dashboard()
    {
        // Force user login

        $this->auth->force_login();

        // Select user info
        $userAccount = $this->userModel->read($this->session->get('email'), 'email');
        unset($userAccount['password']);
        // Prepare Stats dashboard 
        $commentsCounter = $this->commentsModel->count('User_id', $this->session->get('id'));

        // Select comments
        $commentsUser = $this->commentsModel->readLastCommentUser($this->session->get('id'), 'dateAddComment DESC', 10);

        $this->path = '\backend\dashboard\dashboard.html.twig';
        $this->data = ['head' => ['title' => 'Mon compte'], 'user' => $userAccount, 'commentsCounter' => $commentsCounter, 'commentsUser' => $commentsUser];

        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show admin blog
     * 
     * @return \Twig
     */
    public function adminblog()
    {
        // Force user login
        $this->auth->force_admin();


        $AllCommentsActive = $this->commentsModel->count('comment.status', '1');
        $AllCommentsPending = $this->commentsModel->count('comment.status', '0');
        $AllCommentsDisable = $this->commentsModel->count('comment.status', '2');

        $AllUsersAdmin = $this->userModel->count('user.userType', 'admin');
        $AllUsersMember = $this->userModel->count('user.userType', 'member');

        $AllPostsActive = $this->postModel->count('post.status', '1');
        $AllPostsDisable = $this->postModel->count('post.status', '0');
        $AllPostsArchived = $this->postModel->count('post.status', '-1');
        $AllCategories = $this->categoryModel->count('', '');

        $this->path = '\backend\admin\adminblog.html.twig';
        $this->data = ['head' => ['title' => 'Administration Blog'], 'AllCommentsCounterActive' => $AllCommentsActive, 'AllCommentsCounterPending' => $AllCommentsPending, 'AllCommentsCounterDisable' => $AllCommentsDisable, 'AllUsersCounterAdmin' => $AllUsersAdmin, 'AllUsersCounterMember' => $AllUsersMember, 'AllPostsCounterActive' => $AllPostsActive, 'AllPostsCounterDisable' => $AllPostsDisable, 'AllPostsCounterArchived' => $AllPostsArchived, 'AllCategoriesCounter' => $AllCategories];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }


    /**
     * Show post Manager
     * 
     * @return \Twig
     */
    public function postManager()
    {
        // Force user login
        $this->auth->force_admin();
        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();

        $AllPostsActive = $this->postModel->count('post.status', '1');
        $AllPostsDisable = $this->postModel->count('post.status', '0');

        // Pagination 
        $AllPosts = $AllPostsActive + $AllPostsDisable;
        $AllPage = $this->checkAllPage(ceil($AllPosts / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);

        $posts = $this->postModel->readAllPosts("0,1", "id DESC", "$firstPage,$this->itemsByPage");

        $this->path = '\backend\admin\post\postManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des articles'], 'posts' => $posts, 'users' => $users, 'categories' => $categories, 'AllPostsCounterActive' => $AllPostsActive, 'AllPostsCounterDisable' => $AllPostsDisable, 'AllPage' => $AllPage, 'currentPage' => $currentPage];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show post Manager
     * 
     * @return \Twig
     */
    public function commentManager()
    {
        // Force user login
        $this->auth->force_admin();
        $status = 0;

        if ($this->var->query->get('status')) {
            $status = $this->sanitize($this->var->query->get('status'));
        }

        $AllCommentCounter = $this->commentsModel->count("comment.status", "$status");

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllCommentCounter / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);

        $firstPage = $this->firstPage($currentPage, $AllCommentCounter, $this->itemsByPage);

        $comments = $this->commentsModel->readAllCommentsByStatus("$status", "id DESC", "$firstPage,$this->itemsByPage");
        $this->path = '\backend\admin\comment\commentManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des commentaires'], 'comments' => $comments, 'AllCommentCounter' => $AllCommentCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'status' => $status];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show a comment Manager Edit
     * 
     * @return \Twig
     */
    public function commentManagerEdit($param)
    {
        // Force user login
        $this->auth->force_admin();

        $this->path = '\backend\admin\comment\commentEdit.html.twig';
        $comments = $this->commentsModel->readCommentById($param);


        // if no post exist
        if (!$comments) {
            $this->redirect("/error/404");
        }

        $this->data = ['head' => ['title' => "Modifier un commentaire"], 'comments' => $comments];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show post Manager
     * 
     * @return \Twig
     */
    public function categoryManager()
    {
        // Force user login
        $this->auth->force_admin();

        $AllCategoryCounter = $this->categoryModel->count();

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllCategoryCounter / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);

        $firstPage = $this->firstPage($currentPage, $AllCategoryCounter, $this->itemsByPage);

        $categories = $this->categoryModel->readAll("", "id ASC LIMIT $firstPage,$this->itemsByPage");
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
        $this->auth->force_admin();

        $this->path = '\backend\admin\category\categoryEdit.html.twig';
        $category = $this->categoryModel->read($param);


        if (!$category) {
            // if no post 
            $this->redirect("/error/404");
        }
        // if post exist
        $this->data = ['head' => ['title' => "Modifier une catégorie"], 'category' => $category];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show user Manager
     * 
     * @return \Twig
     */
    public function userManager()
    {
        // Force user login
        $this->auth->force_admin();
        $status = "";
        $type = "";
        $AllUserCounter = $this->userModel->count();

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllUserCounter / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);

        $firstPage = $this->firstPage($currentPage, $AllUserCounter, $this->itemsByPage);

        $requete = "";

        if ($this->var->query->get('status') <> "") {
            $status = $this->sanitize($this->var->query->get('status'));
            $requete = "status = '$status'";
        }

        if ($this->var->query->get('type') <> "") {
            $type = $this->sanitize($this->var->query->get('type'));
            if ($requete <> "") {
                $requete .= " AND ";
            }
            $requete .= "userType = '$type'";
        }

        $users = $this->userModel->readAll("$requete", "id ASC LIMIT $firstPage,$this->itemsByPage");

        //print_r($requete);die;

        $this->path = '\backend\admin\user\userManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des utilisateurs'], 'users' => $users, 'AllUserCounter' => $AllUserCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'status' => $status, 'type' => $type];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }


    /**
     * Show a comment Manager Edit
     * 
     * @return \Twig
     */
    public function userManagerEdit($param)
    {
        // Force user login
        $this->auth->force_admin();

        $this->path = '\backend\admin\user\userEdit.html.twig';
        $user = $this->userModel->read($param, 'id');


        // if post exist
        if ($user) {
            $this->data = ['head' => ['title' => "Modifier un utilisateur"], 'user' => $user];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } else {
            // if no post 
            $this->redirect("/error/404");
        }
    }
    /**
     * Show archived post
     * 
     * @return \Twig
     */
    public function postArchived()
    {
        // Force user login
        $this->auth->force_admin();

        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();
        $AllPostsArchived = $this->postModel->count('post.status', '-1');
        $AllPosts = $AllPostsArchived;

        $AllPage = $this->checkAllPage(ceil($AllPosts / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);

        $posts = $this->postModel->readAllPosts("-1", 'id DESC', "$firstPage,$this->itemsByPage");

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
        $this->auth->force_admin();

        $this->path = '\backend\admin\post\postEdit.html.twig';
        $posts = $this->postModel->readPostById($param);

        // if post exist
        if ($posts) {
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
        } else {
            // if no post 
            $this->redirect("/error/404");
        }
    }
}
