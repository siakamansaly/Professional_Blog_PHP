<?php
use Blog\Controllers\SessionController;

define('ROOT_CONTROLLER', 'Blog\Controllers\\');
require_once "./../vendor/autoload.php";
date_default_timezone_set('Europe/Paris');

// Active environment variable
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__.'./../');
$dotenv->load();

$session = new SessionController();
$session->sessionStart();

// Start Router
$router = new AltoRouter();

// Add all routes
$router->addRoutes(array(
    array('GET', '/', ROOT_CONTROLLER . 'HomeController#index', 'home'),
    array('GET', '/blog', ROOT_CONTROLLER . 'HomeController#blog', 'blog'),
    array('GET', '/post/[*:slug]', ROOT_CONTROLLER . 'HomeController#post', 'post'),
    array('GET', '/policy', ROOT_CONTROLLER . 'HomeController#policy', 'policy'),
    array('GET', '/legalNotice', ROOT_CONTROLLER . 'HomeController#legalNotice', 'legalNotice'),
    array('GET', '/renew/[*:token]', ROOT_CONTROLLER . 'HomeController#renewPassword', 'renewPassword'),
    array('GET', '/error/[*:code]', ROOT_CONTROLLER . 'HomeController#errorPage', 'errorPage'),

    array('GET', '/dashboard', ROOT_CONTROLLER . 'BackController#dashboard', 'dashboard'),
    array('GET', '/adminBlog', ROOT_CONTROLLER . 'BackController#adminblog', 'adminblog'),
    array('POST', '/contact', ROOT_CONTROLLER . 'BackController#contact', 'contact'),

    array('GET', '/postManager', ROOT_CONTROLLER . 'PostController#postManager', 'postManager'),
    array('GET', '/postArchived', ROOT_CONTROLLER . 'PostController#postArchived', 'postArchived'),
    array('GET', '/postManagerEdit/[*:id]', ROOT_CONTROLLER . 'PostController#postManagerEdit', 'postManagerEdit'),
    array('POST', '/postAdd', ROOT_CONTROLLER . 'PostController#postAdd', 'postAdd'),
    array('POST', '/postEdit', ROOT_CONTROLLER . 'PostController#postEdit', 'postEdit'),
    array('POST', '/postDelete', ROOT_CONTROLLER . 'PostController#postDelete', 'postDelete'),

    array('GET', '/categoryManager', ROOT_CONTROLLER . 'CategoryController#categoryManager', 'categoryManager'),
    array('GET', '/categoryManagerEdit/[*:id]', ROOT_CONTROLLER . 'CategoryController#categoryManagerEdit', 'categoryManagerEdit'),
    array('POST', '/categoryAdd', ROOT_CONTROLLER . 'CategoryController#categoryAdd', 'categoryAdd'),
    array('POST', '/categoryEdit', ROOT_CONTROLLER . 'CategoryController#categoryEdit', 'categoryEdit'),
    array('POST', '/categoryDelete', ROOT_CONTROLLER . 'CategoryController#categoryDelete', 'categoryDelete'),

    array('POST', '/commentAdd', ROOT_CONTROLLER . 'CommentController#commentAdd', 'commentAdd'),
    array('POST', '/commentEdit', ROOT_CONTROLLER . 'CommentController#commentEdit', 'commentEdit'),
    array('POST', '/commentValidate', ROOT_CONTROLLER . 'CommentController#commentValidate', 'commentValidate'),
    array('POST', '/commentDisable', ROOT_CONTROLLER . 'CommentController#commentDisable', 'commentDisable'),
    array('POST', '/commentDelete', ROOT_CONTROLLER . 'CommentController#commentDelete', 'commentDelete'),
    array('GET', '/commentManager', ROOT_CONTROLLER . 'CommentController#commentManager', 'commentManager'),
    array('GET', '/commentManagerEdit/[*:id]', ROOT_CONTROLLER . 'CommentController#commentManagerEdit', 'commentManagerEdit'),

    array('POST', '/userValidate', ROOT_CONTROLLER . 'UserController#userValidate', 'userValidate'),
    array('POST', '/userDisable', ROOT_CONTROLLER . 'UserController#userDisable', 'userDisable'),
    array('POST', '/userDelete', ROOT_CONTROLLER . 'UserController#userDelete', 'userDelete'),
    array('GET', '/userManager', ROOT_CONTROLLER . 'UserController#userManager', 'userManager'),
    array('GET', '/userManagerEdit/[*:id]', ROOT_CONTROLLER . 'UserController#userManagerEdit', 'userManagerEdit'),

    array('POST', '/editProfile', ROOT_CONTROLLER . 'UserController#editProfile', 'editProfile'),
    array('POST', '/editPicture', ROOT_CONTROLLER . 'UserController#editPicture', 'editPicture'),
    
    array('POST', '/lostPassword', ROOT_CONTROLLER . 'AuthController#lostPassword', 'lostPassword'),
    array('POST', '/register', ROOT_CONTROLLER . 'AuthController#register', 'register'),
    array('POST', '/savePassword', ROOT_CONTROLLER . 'AuthController#savePassword', 'savePassword'),
    array('GET', '/logout', ROOT_CONTROLLER . 'AuthController#logout', 'logout'),
    array('POST', '/login', ROOT_CONTROLLER . 'AuthController#login', 'login')
    
));

// Match url
$match = $router->match();

// call closure or throw 404 status
if (is_array($match)) {

    // Extract Class and method
    list($controller_class, $action) = explode('#', $match['target']);
    // Verify if method is callable, display page 
    if (is_callable(array($controller_class, $action))) {
        $controller = new $controller_class;
        
        call_user_func_array(array($controller, $action), $match['params']);
        
    } else {
        $controller = new Blog\Controllers\HomeController;
        $controller->redirect("/error/405",405);
    }
} else {
    // no route was matched
    $controller = new Blog\Controllers\HomeController;
    $controller->redirect("/error/404", 404);
}


