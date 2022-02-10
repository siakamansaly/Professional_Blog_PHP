<?php
define('BASE_PATH', realpath(__DIR__ . '/../'));
define('ROOT_CONTROLLER', 'Blog\Controllers\\');
require_once BASE_PATH . '/vendor/autoload.php';
Blog\Controllers\SessionController::sessionStart();
$token = md5(rand(1000, 9999));
date_default_timezone_set('Europe/Paris');

// Debug mode
/*$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();*/

// Active environment variable
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Start Router
$router = new AltoRouter();

// Add all routes
$router->addRoutes(array(
    array('GET', '/', ROOT_CONTROLLER . 'HomeController#index', 'home'),
    array('GET', '/blog', ROOT_CONTROLLER . 'HomeController#blog', 'blog'),
    array('GET', '/post/[*:slug]', ROOT_CONTROLLER . 'HomeController#post', 'post'),
    array('GET', '/sitemap', ROOT_CONTROLLER . 'HomeController#sitemap', 'sitemap'),
    array('GET', '/legalNotice', ROOT_CONTROLLER . 'HomeController#legalNotice', 'legalNotice'),
    array('GET', '/dashboard', ROOT_CONTROLLER . 'HomeController#dashboard', 'dashboard'),
    array('GET', '/adminBlog', ROOT_CONTROLLER . 'HomeController#adminblog', 'adminblog'),

    array('POST', '/contact', ROOT_CONTROLLER . 'ContactController#contact', 'contact'),

    array('POST', '/lostPassword', ROOT_CONTROLLER . 'AuthController#lostPassword', 'lostPassword'),
    array('POST', '/register', ROOT_CONTROLLER . 'AuthController#register', 'register'),
    array('GET', '/renew/[*:token]', ROOT_CONTROLLER . 'HomeController#renewPassword', 'renewPassword'),
    array('POST', '/savePassword', ROOT_CONTROLLER . 'AuthController#savePassword', 'savePassword'),
    array('GET', '/logout', ROOT_CONTROLLER . 'AuthController#logout', 'logout'),


    array('GET', '/postManager', ROOT_CONTROLLER . 'HomeController#postManager', 'postManager'),
    array('GET', '/postArchived', ROOT_CONTROLLER . 'HomeController#postArchived', 'postArchived'),
    array('GET', '/postManagerEdit/[*:id]', ROOT_CONTROLLER . 'HomeController#postManagerEdit', 'postManagerEdit'),
    array('POST', '/postAdd', ROOT_CONTROLLER . 'PostController#postAdd', 'postAdd'),
    array('POST', '/postEdit', ROOT_CONTROLLER . 'PostController#postEdit', 'postEdit'),
    array('POST', '/postDelete', ROOT_CONTROLLER . 'PostController#postDelete', 'postDelete'),

    array('GET', '/categoryManager', ROOT_CONTROLLER . 'HomeController#categoryManager', 'categoryManager'),
    array('GET', '/categoryManagerEdit/[*:id]', ROOT_CONTROLLER . 'HomeController#categoryManagerEdit', 'categoryManagerEdit'),
    array('POST', '/categoryAdd', ROOT_CONTROLLER . 'PostCategoryController#categoryAdd', 'categoryAdd'),
    array('POST', '/categoryEdit', ROOT_CONTROLLER . 'PostCategoryController#categoryEdit', 'categoryEdit'),
    array('POST', '/categoryDelete', ROOT_CONTROLLER . 'PostCategoryController#categoryDelete', 'categoryDelete'),

    array('POST', '/commentAdd', ROOT_CONTROLLER . 'CommentController#commentAdd', 'commentAdd'),
    array('POST', '/commentEdit', ROOT_CONTROLLER . 'CommentController#commentEdit', 'commentEdit'),
    array('POST', '/commentValidate', ROOT_CONTROLLER . 'CommentController#commentValidate', 'commentValidate'),
    array('POST', '/commentDisable', ROOT_CONTROLLER . 'CommentController#commentDisable', 'commentDisable'),
    array('POST', '/commentDelete', ROOT_CONTROLLER . 'CommentController#commentDelete', 'commentDelete'),
    array('GET', '/commentManager', ROOT_CONTROLLER . 'HomeController#commentManager', 'commentManager'),
    array('GET', '/commentManagerEdit/[*:id]', ROOT_CONTROLLER . 'HomeController#commentManagerEdit', 'commentManagerEdit'),

    array('POST', '/userValidate', ROOT_CONTROLLER . 'UserController#userValidate', 'userValidate'),
    array('POST', '/userDisable', ROOT_CONTROLLER . 'UserController#userDisable', 'userDisable'),
    array('POST', '/userDelete', ROOT_CONTROLLER . 'UserController#userDelete', 'userDelete'),
    array('GET', '/userManager', ROOT_CONTROLLER . 'HomeController#userManager', 'userManager'),
    array('GET', '/userManagerEdit/[*:id]', ROOT_CONTROLLER . 'HomeController#userManagerEdit', 'userManagerEdit'),

    array('POST', '/editProfile', ROOT_CONTROLLER . 'UserController#editProfile', 'editProfile'),
    array('POST', '/editPicture', ROOT_CONTROLLER . 'UserController#editPicture', 'editPicture'),

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
        $controller->error();
    }
} else {
    // no route was matched
    $controller = new Blog\Controllers\HomeController;
    $controller->error();
}
//print_r($_SESSION);

