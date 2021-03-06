<?php

namespace Blog\Controllers;

class HomeController extends Controller
{
    protected $modelName = \Blog\Models\Contact::class;
    private $path;
    private $data;
    private $postModel;
    private $categoryModel;
    private $commentsModel;
    private $postcategoryModel;
    protected $auth;

    public function __construct()
    {
        parent::__construct();

        $this->postModel = new \Blog\Models\Post;
        $this->categoryModel = new \Blog\Models\Category;
        $this->commentsModel = new \Blog\Models\Comment;
        $this->postcategoryModel = new \Blog\Models\PostCategory;
    }


    /**
     * Show index page
     * @return \Twig
     */
    public function index()
    {
        $this->path = '\frontend\homepage.html.twig';
        $posts = new \Blog\Models\Post;
        $posts = $this->postModel->readPostsRecent();
        $counter=0;
        foreach ($posts as $post) {
            $name="";
            $categoryPosts = $this->postcategoryModel->readAllCategoriesByPost($post["id"]);
            foreach ($categoryPosts as $categoryPost) {
                $name.= $categoryPost["name"].",";
            }
            $posts[$counter]["categories"] = substr($name,0,-1);
            $counter++;
        }
        $this->data = ['head' => ['title' => 'Home'], 'posts' => $posts];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }
    /**
     * Show sitemap page
     * @return \Twig
     */
    public function policy()
    {
        $this->path = '\frontend\policy.html.twig';
        $this->data = ['head' => ['title' => 'Politique de confidentialité']];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show legal Notice page
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
     * @return \Twig
     */
    public function blog()
    {

        $this->path = '\frontend\blog.html.twig';
        $currentCategory = 0;

        // Pagination 
        $AllPosts = $this->postModel->count('post.status', '1');
        $AllPage = $this->checkAllPage(ceil($AllPosts / $this->itemsByPage));
        $currentPage = $this->currentPage($AllPage);
        $firstPage = $this->firstPage($currentPage, $AllPosts, $this->itemsByPage);
        if (!empty($this->var->query->get('category'))) {
            $currentCategory = (int) $this->sanitize(($this->var->query->get('category')));
        }
        $posts = $this->postModel->readAllPostsByCategory("1", $currentCategory, "post.dateAddPost DESC", "$firstPage,$this->itemsByPage");
        $categoryPosts = [];
        $counter=0;
        foreach ($posts as $post) {
            $name="";
            $categoryPosts = $this->postcategoryModel->readAllCategoriesByPost($post["id"]);
            foreach ($categoryPosts as $categoryPost) {
                $name.= $categoryPost["name"].",";
            }
            $posts[$counter]["categories"] = substr($name,0,-1);
            $counter++;
        }

        $postsAllCategory = $this->categoryModel->readAll();

        $this->data = ['head' => ['title' => 'Blog'], 'posts' => $posts, 'postsAllCategory' => $postsAllCategory, 'AllPage' => $AllPage, 'currentPage' => $currentPage, 'currentCategory' => $currentCategory];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show a post page
     * @return \Twig
     */
    public function post(string $param)
    {
        $this->path = '\frontend\post.html.twig';
        $posts = $this->postModel->readPostBySlug($param);

        if (!$posts) {
            // if no post 
            $this->redirect("/error/404");
        }

        // if post exist
        $sidebar = false;
        $writeComment = false;
        $this->auth = new AuthController;
        if ($this->auth->isAdmin()) {
            $sidebar = true;
        }
        if ($this->auth->isLogin()) {
            $writeComment = true;
        }
        $postsCategory = $this->postcategoryModel->readAllCategoriesByPost($posts['id']);
        $postsAllCategory = $this->categoryModel->readAll();
        $recentPosts = $this->postModel->readPostsRecent($posts['id']);

        $commentsParent = $this->commentsModel->readAllCommentsParent($posts['id']);
        $commentsChild = $this->commentsModel->readAllCommentsChild($posts['id']);

        $this->data = ['head' => ['title' => $posts['title']], 'post' => $posts, 'postsCategory' => $postsCategory, 'postsAllCategory' => $postsAllCategory, 'recentPosts' => $recentPosts, 'commentsParent' => $commentsParent, 'commentsChild' => $commentsChild, 'sidebar' => $sidebar, 'writeComment' => $writeComment];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Renew Password user page
     * @return \Twig
     */
    public function renewPassword($param)
    {
        $this->path = '\core\renewPassword.html.twig';
        $token = new \Blog\Models\User;
        $token = $token->read($param, "token");

        // if token exist
        if (!$token) {
            // if no token 
            $this->redirect("/error/498");
        }
        $this->data = ['head' => ['title' => "Renouveler mot de Passe"], 'token' => $token];
        $this->setResponseHttp(200);
        $this->render($this->path, $this->data);
    }

    /**
     * Show error page
     * @return \Twig
     */
    public function errorPage(int $code = 404)
    {
        $error = [];
        switch ($code) {
            case 401:
                $error["code"] = 401;
                $error["message"] = "Utilisateur non authentifié.";
                break;
            case 403:
                $error["code"] = 403;
                $error["message"] = "Accès refusé.";
                break;
            case 405:
                $error["code"] = 405;
                $error["message"] = "Méthode de requête non autorisée.";
                break;
            case 498:
                $error["code"] = 498;
                $error["message"] = "Le jeton a expiré ou est invalide.";
                break;

            default:
                $error["code"] = 404;
                $error["message"] = "Cette page n'existe pas ou est invalide.";
                break;
        }

        $path = '\core\404.html.twig';
        $data = ['head' => ['title' => 'Erreur 404'], 'error' => $error];

        $this->setResponseHttp($code);
        $this->render($path, $data);
    }
}
