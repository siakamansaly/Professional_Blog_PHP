<?php

namespace Blog\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Blog\Controllers\Globals;


class BackController extends Controller
{
    protected $modelName = \Blog\Models\Contact::class;
    private $path;
    private $data;
    private $userModel;
    private $postModel;
    private $categoryModel;
    private $commentsModel;
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
     * Check contact form and send message
     * 
     * @return void
     */
    public function contact()
    {   
        $ENV = new Globals;
        $titleWebSite = $ENV->env("TITLE_WEBSITE");
    
        $this->data = [];
        $json=[];
        $message="";
        $success="";
        if (empty($this->var->request->all())) {
            $this->redirect("/error/405");
        }
        $this->data['firstName'] = $this->sanitize($this->var->request->get('prenom'));
        $this->data['lastName'] = $this->sanitize($this->var->request->get('name'));
        $this->data['email'] = $this->sanitize($this->var->request->get('email'));
        $this->data['message'] = $this->sanitize($this->var->request->get('message'));

        $this->data['subject'] = $titleWebSite . ' - Formulaire de contact';
        $message = $this->div_alert("Message envoyé avec succès !","success");
        $success=$this->sendMessage($this->data);
        $json['success']=$success;
        $json['message']=$message;
        $response = new JsonResponse($json);
        $response->send();
        
    }

    

    
    
}