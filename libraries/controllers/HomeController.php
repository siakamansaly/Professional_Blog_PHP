<?php

namespace Blog\Controllers;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    private $contactModel;
    protected $modelName = \Models\Contact::class;
    private $path;
    private $data;
    private $userModel;
    private $postModel;
    private $categoryModel;
    private $commentsModel;
    private $postcategoryModel;
    private $itemsByPage = 9;
    public $var;

    public function __construct()
    {
        $this->contactModel = new \Blog\Models\Contact;
        $this->userModel = new \Blog\Models\User;
        $this->postModel = new \Blog\Models\Post;
        $this->categoryModel = new \Blog\Models\PostCategory;
        $this->commentsModel = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\Post_PostCategory;
        $this->var = Request::createFromGlobals();
    }


    /**
     * Show index page
     * 
     * @return \Twig
     */
    public function index(array $dataForm = [])
    {
        $this->path = '\frontend\homepage.html.twig';
        $posts = new \Blog\Models\Post;
        $posts = $this->postModel->readPostsRecent();

        $this->data = ['head' => ['title' => 'Home'], 'posts' => $posts, 'dataForm' => $dataForm];
        //var_dump($this->data);die;
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }
    /**
     * Show sitemap page
     * 
     * @return \Twig
     */
    public function sitemap()
    {
        $this->path = '\frontend\sitemap.html.twig';
        $this->data = ['head' => ['title' => 'Plan du site']];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show legal Notice page
     * 
     * @return \Twig
     */
    public function legalNotice()
    {
        $this->path = '\frontend\legalNotice.html.twig';
        $this->data = ['head' => ['title' => 'Mentions légales']];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show blog page
     * 
     * @return \Twig
     */
    public function blog()
    {

        $this->path = '\frontend\blog.html.twig';
        $currentCategory ="";

        // Pagination 
        $AllPosts = $this->postModel->count('post.status', '1');
        $AllPage = $this->checkAllPage(ceil($AllPosts/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);
        if(empty($this->var->query->get('category')))
        {
            $posts = $this->postModel->readAllPosts("1","","$firstPage,$this->itemsByPage");
        }
        else{
            $currentCategory = (int) $this->sanitize(($this->var->query->get('category')));
            $posts = $this->postModel->readAllPostsByCategory("1",$currentCategory,"","$firstPage,$this->itemsByPage");
            
        }
        

        
        $postsAllCategory = $this->categoryModel->readAll();

        $this->data = ['head' => ['title' => 'Blog'], 'posts' => $posts, 'postsAllCategory' => $postsAllCategory, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'currentCategory' => $currentCategory];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show a post page
     * 
     * @return \Twig
     */
    public function post(string $param)
    {
        $this->path = '\frontend\post.html.twig';
        $posts = $this->postModel->readPostBySlug($param);


        // if post exist
        if (!empty($posts)) {

            $postsCategory = $this->postcategoryModel->readAllCategoriesByPost($posts['id']);
            $postsAllCategory = $this->categoryModel->readAll();
            $recentPosts = $this->postModel->readPostsRecent($posts['id']);
            
            $commentsParent = $this->commentsModel->readAllCommentsParent($posts['id']);
            $commentsChild = $this->commentsModel->readAllCommentsChild($posts['id']);

            $this->data = ['head' => ['title' => $posts['title']], 'post' => $posts, 'postsCategory' => $postsCategory, 'postsAllCategory' => $postsAllCategory, 'recentPosts' => $recentPosts, 'commentsParent' => $commentsParent, 'commentsChild' => $commentsChild];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } else { // if no post 
            $this->error();
        }
    }

    /**
     * Show index page
     * 
     * @return \Twig
     */
    public function dashboard()
    {
        // Force user login
        AuthController::force_login();

        // Select user info
        $userAccount = $this->userModel->read(SessionController::get('email', 'login'), 'email');
        unset($userAccount['password']);
        // Prepare Stats dashboard 
        $commentsCounter = $this->commentsModel->count('User_id', SessionController::get('id', 'login'));

        // Select comments
        $commentsUser = $this->commentsModel->readLastCommentUser(SessionController::get('id', 'login'), 'dateAddComment DESC',10);

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
        AuthController::force_login();
        AuthController::is_admin();

        $AllCommentsCounterActive = $this->commentsModel->count('comment.status', '1');
        $AllCommentsCounterPending = $this->commentsModel->count('comment.status', '0');
        $AllCommentsCounterDisable = $this->commentsModel->count('comment.status', '2');

        $AllUsersCounterAdmin = $this->userModel->count('user.userType', 'admin');
        $AllUsersCounterMember = $this->userModel->count('user.userType', 'member');

        $AllPostsCounterActive = $this->postModel->count('post.status', '1');
        $AllPostsCounterDisable = $this->postModel->count('post.status', '0');
        $AllPostsCounterArchived = $this->postModel->count('post.status', '-1');
        $AllCategoriesCounter = $this->categoryModel->count('', '');

        $this->path = '\backend\admin\adminblog.html.twig';
        $this->data = ['head' => ['title' => 'Administration Blog'], 'AllCommentsCounterActive' => $AllCommentsCounterActive, 'AllCommentsCounterPending' => $AllCommentsCounterPending, 'AllCommentsCounterDisable' => $AllCommentsCounterDisable, 'AllUsersCounterAdmin' => $AllUsersCounterAdmin, 'AllUsersCounterMember' => $AllUsersCounterMember, 'AllPostsCounterActive' => $AllPostsCounterActive, 'AllPostsCounterDisable' => $AllPostsCounterDisable, 'AllPostsCounterArchived' => $AllPostsCounterArchived, 'AllCategoriesCounter' => $AllCategoriesCounter];
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
        AuthController::force_login();
        AuthController::is_admin();
        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();

        $AllPostsCounterActive = $this->postModel->count('post.status', '1');
        $AllPostsCounterDisable = $this->postModel->count('post.status', '0');

        // Pagination 
        $AllPosts = $AllPostsCounterActive + $AllPostsCounterDisable;
        $AllPage = $this->checkAllPage(ceil($AllPosts/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);
        
        $posts = $this->postModel->readAllPosts("0,1", "id DESC","$firstPage,$this->itemsByPage");

        $this->path = '\backend\admin\post\postManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des articles'], 'posts' => $posts, 'users' => $users, 'categories' => $categories, 'AllPostsCounterActive' => $AllPostsCounterActive, 'AllPostsCounterDisable' => $AllPostsCounterDisable, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'delete'=>['title'=>'Supprimer cet article','class'=>'formPostDelete','subtitle'=>'Êtes-vous sûr de vouloir supprimer cet article ?']];
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
        AuthController::force_login();
        AuthController::is_admin();
        $status="";
        
        if(!empty($this->var->query->get('status')))
        {
            $status = $this->sanitize($this->var->query->get('status'));
        }
        else{
            $status = 0;
        }
        $AllCommentCounter = $this->commentsModel->count("comment.status", "$status");

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllCommentCounter/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        
        $firstPage = $this->firstPage($currentPage, $AllCommentCounter, $this->itemsByPage);
        
        $comments = $this->commentsModel->readAllCommentsByStatus("$status", "id DESC","$firstPage,$this->itemsByPage");
        $this->path = '\backend\admin\comment\commentManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des commentaires'], 'comments' => $comments, 'AllCommentCounter' => $AllCommentCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'status' => $status, 'delete'=>['title'=>'Supprimer ce commentaire','class'=>'formCommentDelete','subtitle'=>'Êtes-vous sûr de vouloir supprimer ce commentaire ?']];
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
        $this->path = '\backend\admin\comment\commentEdit.html.twig';
        $comments = $this->commentsModel->readCommentById($param);

    
        // if post exist
        if (!empty($comments)) {
            $this->data = ['head' => ['title' => "Modifier un commentaire"], 'comments' => $comments];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } else { // if no post 
            $this->error();
        }
    }

    /**
     * Show post Manager
     * 
     * @return \Twig
     */
    public function categoryManager()
    {
        // Force user login
        AuthController::force_login();
        AuthController::is_admin();
        
        $AllCategoryCounter = $this->categoryModel->count();

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllCategoryCounter/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        
        $firstPage = $this->firstPage($currentPage, $AllCategoryCounter, $this->itemsByPage);
        
        $categories = $this->categoryModel->readAll("","id ASC LIMIT $firstPage,$this->itemsByPage");
        $this->path = '\backend\admin\category\categoryManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des catégories'], 'categories' => $categories, 'AllCategoryCounter' => $AllCategoryCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'delete'=>['title'=>'Supprimer cette catégorie','class'=>'formCategoryDelete','subtitle'=>'Êtes-vous sûr de vouloir supprimer cette catégorie ?']];
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
        $this->path = '\backend\admin\category\categoryEdit.html.twig';
        $category = $this->categoryModel->read($param);
    
        // if post exist
        if (!empty($category)) {
            $this->data = ['head' => ['title' => "Modifier une catégorie"], 'category' => $category];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } else { // if no post 
            $this->error();
        }
    }

    /**
     * Show user Manager
     * 
     * @return \Twig
     */
    public function userManager()
    {
        // Force user login
        AuthController::force_login();
        AuthController::is_admin();
        
        $AllUserCounter = $this->userModel->count();

        // Pagination 
        $AllPage = $this->checkAllPage(ceil($AllUserCounter/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        
        $firstPage = $this->firstPage($currentPage, $AllUserCounter, $this->itemsByPage);

        $requete = "";

        if($this->var->query->get('status')<>"")
        {
            $status = $this->sanitize($this->var->query->get('status'));
            $requete= "status = '$status'";
        }
        else
        {
            $status ="";
        }
        if($this->var->query->get('type')<>"")
        {
            $type = $this->sanitize($this->var->query->get('type'));
            if($requete<>""){$requete.=" AND ";}
            $requete.= "userType = '$type'";
        }
        else
        {
            $type ="";
        }
        $users = $this->userModel->readAll("$requete","id ASC LIMIT $firstPage,$this->itemsByPage");

        //print_r($requete);die;
        
        $this->path = '\backend\admin\user\userManager.html.twig';
        $this->data = ['head' => ['title' => 'Administration des utilisateurs'], 'users' => $users, 'AllUserCounter' => $AllUserCounter, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'status' => $status, 'type' => $type, 'delete'=>['title'=>'Supprimer cet utilisateur','class'=>'formUserDelete','subtitle'=>'Êtes-vous sûr de vouloir supprimer cet utilisateur ?']];
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
        $this->path = '\backend\admin\user\userEdit.html.twig';
        $user = $this->userModel->read($param,'id');

    
        // if post exist
        if (!empty($user)) {
            $this->data = ['head' => ['title' => "Modifier un commentaire"], 'user' => $user];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } else { // if no post 
            $this->error();
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
        AuthController::force_login();
        AuthController::is_admin();
        $users = $this->userModel->readAllAuthors();
        $categories = $this->categoryModel->readAll();
        $AllPostsCounterArchived = $this->postModel->count('post.status', '-1');
        $AllPosts = $AllPostsCounterArchived;
        
        $AllPage = $this->checkAllPage(ceil($AllPosts/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);

        $posts = $this->postModel->readAllPosts("-1", 'id DESC',"$firstPage,$this->itemsByPage");
        
        $this->path = '\backend\admin\post\postArchived.html.twig';
        $this->data = ['head' => ['title' => 'Articles archivés'], 'posts' => $posts, 'users' => $users, 'categories' => $categories, 'AllPostsCounterArchived' => $AllPostsCounterArchived, 'AllPage' => $AllPage, 'currentPage' => $currentPage];
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
        $this->path = '\backend\admin\post\postEdit.html.twig';
        $posts = $this->postModel->readPostById($param);

        // if post exist
        if (!empty($posts)) {
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
        } else { // if no post 
            $this->error();
        }
    }

    /**
     * Show a post page
     * 
     * @return \Twig
     */
    public function renewPassword($param)
    {
        $this->path = '\core\renewPassword.html.twig';
        $token = new \Blog\Models\User;
        $token = $token->read($param, "token");

        // if token exist
        if (!empty($token)) {
            $this->data = ['head' => ['title' => "Renouveler mot de Passe"], 'token' => $token];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } else { // if no token 
            $this->error();
        }
    }
}
