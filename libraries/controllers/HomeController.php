<?php

namespace Blog\Controllers;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
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

    public function __construct()
    {
        parent::__construct();
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
    public function index()
    {
        $this->path = '\frontend\homepage.html.twig';
        $posts = new \Blog\Models\Post;
        $posts = $this->postModel->readPostsRecent();
        $this->data = ['head' => ['title' => 'Home'], 'posts' => $posts];
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
        $currentCategory =0;

        // Pagination 
        $AllPosts = $this->postModel->count('post.status', '1');
        $AllPage = $this->checkAllPage(ceil($AllPosts/$this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);
        if(!empty($this->var->query->get('category')))
        {
            $currentCategory = (int) $this->sanitize(($this->var->query->get('category')));
        }
        $posts = $this->postModel->readAllPostsByCategory("1",$currentCategory,"post.dateAddPost DESC","$firstPage,$this->itemsByPage");
            
        
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
            $sidebar = false;
            if(AuthController::is_admin())
            {
                $sidebar = true;
            }
            $postsCategory = $this->postcategoryModel->readAllCategoriesByPost($posts['id']);
            $postsAllCategory = $this->categoryModel->readAll();
            $recentPosts = $this->postModel->readPostsRecent($posts['id']);
            
            $commentsParent = $this->commentsModel->readAllCommentsParent($posts['id']);
            $commentsChild = $this->commentsModel->readAllCommentsChild($posts['id']);

            $this->data = ['head' => ['title' => $posts['title']], 'post' => $posts, 'postsCategory' => $postsCategory, 'postsAllCategory' => $postsAllCategory, 'recentPosts' => $recentPosts, 'commentsParent' => $commentsParent, 'commentsChild' => $commentsChild, 'sidebar' => $sidebar];
            $this->setResponseHttp(200);
            $this->render($this->path, $this->data);
        } 
         // if no post 
        $this->error(404);
        
    }

    /**
     * Renew Password user
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
        } 
        // if no token 
        $this->error(498);
    
    }
}
