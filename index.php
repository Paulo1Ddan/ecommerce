<?php
session_start();
//session_destroy();
require_once 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Class\Page;
use Class\PageAdmin;
use Class\Model\User;
use Class\Model\Cat;


$app = AppFactory::create();

//Site
//Home page - GET
$app->get('/ecommerce/', function (Request $request, Response $response, $args) {
    $page = new Page();
    $page->setTpl('index');
    return $response;
});

//Categorias
$app->get("/ecommerce/category/{idcategory}", function (Request $request, Response $response, $args) {
    $cat = new Cat();

    $idcategory = $args['idcategory'];

    $cat->get($idcategory);

    $page = new Page();

    $page->setTpl("category", array(
        "category" => $cat->getData()
    ));

    return $response;

});


//Admin
//Admin page - GET
$app->get('/ecommerce/admin', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>history.back()</script>";
        exit;
    }
    $page = new PageAdmin();
    $page->setTpl('index');
    return $response;
});


//Admin Login page - GET
$app->get('/ecommerce/admin/login', function (Request $request, Response $response, $args) {
    if (User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/admin'</script>";
        exit;
    }
    $page = new PageAdmin(array(
        "header" => false,
        'footer' => false
    ));

    $page->setTpl('login');

    return $response;
});

//Admin login - POST
$app->post('/ecommerce/admin/login', function (Request $request, Response $response, $args) {
    User::login($_POST['login'], $_POST['password']);

    if (User::verifyLogin()) {
        header("Location: /ecommerce/admin");
        echo "<script>location.href='/ecommerce/admin'</script>";
        exit();
    }else{
        header("Location: /ecommerce/admin/login");
        echo "<script>location.href='/ecommerce/admin'</script>";
        exit();
    }
    return $response;
});

//Admin logout - GET
$app->get('/ecommerce/admin/logout', function (Request $request, Response $response) {
    if (User::logout()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce'</script>";
        exit();
    }
    return $response;
});


//Admin users - GET
$app->get('/ecommerce/admin/users', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $users = User::listAll();
    $page = new PageAdmin();
    $page->setTpl('users', array("users" => $users));
    return $response;
});


//Admin create user - GET
$app->get('/ecommerce/admin/users/create', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }

    $page = new PageAdmin();
    $page->setTpl('users-create');
    return $response;
});

//Admin create user - POST
$app->post('/ecommerce/admin/users/create', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        echo "<script>location.href='/ecommerce/'</script>";
        header("Location: /ecommerce/");
        exit();
    }

    $user = new User();

    $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

        "cost"=>12

    ]);

    $user->setData($_POST);

    if ($user->save()) {
        header("Location: /ecommerce/admin/users");
        exit();
    } else {
        header("Location: /ecommerce/admin/users/create");
        exit();
    }

    return $response;
});


//Admin delete user - GET
$app->get('/ecommerce/admin/users/{iduser}/delete', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    $iduser = $args['iduser'];

    $user = new User();

    if($user->delete($iduser)){
        header("Location: /ecommerce/admin/users");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }else{
        header("Location: /ecommerce/admin/users");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
   
    return $response;

});


//Admin user by id - get
$app->get('/ecommerce/admin/users/{iduser}', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $iduser = $args['iduser'];
    $page = new PageAdmin();
    $user = new User();
    
    $data = $user->get($iduser);

    $page->setTpl('users-update', array(
        "user" => $data
    ));
    return $response;
});

//Admin update user - POST
$app->post('/ecommerce/admin/users/{iduser}', function (Request $request, Response $response, $args) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }

    $iduser = $args['iduser'];

    $user = new User();

    $data = $user->get($iduser);
    
    $_POST['inadmin'] = (isset($_POST['inadmin'])?1:0);
    $_POST['iduser'] = $iduser;
    $_POST['despassword'] = $data['despassword'];

    $user->setData($_POST);
    
    if ($user->update()) {
        header("Location: /ecommerce/admin/users");
        exit();
    } else {
        header("Location: /ecommerce/admin/users/$iduser");
        exit();
    }
});


//Forgot Password
//Get
$app->get("/ecommerce/admin/forgot", function(Request $request, Response $response) {
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $page->setTpl("forgot");

    return $response;
});

//Post
$app->post("/ecommerce/admin/forgot", function(Request $request, Response $response){
    
    $email = $_POST['email'];
    $user = User::getForgot($email);
    if($user){
        header("Location: /ecommerce/admin/forgot/sent");
        exit();
    }else{
        header("Location: /ecommerce/admin/forgot");
        echo "<script>history.back()</script>";
        exit;
    }
    return $response;
});

//Sent
$app->get("/ecommerce/admin/forgot/sent", function(Request $request, Response $response){
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $page->setTpl("forgot-sent");
    
    return $response;
});

//Reset
$app->get("/ecommerce/admin/forgot/reset", function(Request $request, Response $response){
    $user = User::validForgotDecripty($_GET['code']);
    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $page->setTpl("forgot-reset", array(
        "name" => $user['desperson'],
        "code" => $_GET['code']
    ));
    
    return $response;
});

//Reset POST
$app->post('/ecommerce/admin/forgot/reset', function(Request $request, Response $response){
    $forgot = User::validForgotDecripty($_POST['code']);

    $iduser = $forgot['iduser'];

    $user = new User();

    $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT, ["cost"=>12]);

    if($user->setPassword($_POST['password'], $iduser)){
        User::setForgotUsed($forgot['idrecovery']);
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("forgot-reset-success");
    }else{
        echo "erro";
    }

    return $response;
});


//Categorias
$app->get('/ecommerce/admin/categories', function(Request $request, Response $response) {
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    $page = new PageAdmin();

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $cat = Cat::listAll();

    $page->setTpl("categories", array(
        "categories" => $cat
    ));

    return $response;

});

//Create
$app->get('/ecommerce/admin/categories/create', function(Request $request, Response $response){
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    $page = new PageAdmin();

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $page->setTpl("categories-create");

    return $response;
});
$app->post('/ecommerce/admin/categories/create', function(Request $request, Response $response){
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    $cat = new Cat();

    $cat->setData($_POST);

    if($cat->save()){
        header("Location: /ecommerce/admin/categories");
        exit();
    }else{
        header("Location: /ecommerce/admin/categories/create");
        exit();
    }

    return $response;

});

//Delete
$app->get("/ecommerce/admin/categories/{idcategory}/delete", function(Request $request, Response $response, $args){
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    $idcategory = $args['idcategory'];

    $cat = new Cat();

    $cat->get($idcategory);

    if($cat->delete()){
        header("Location: /ecommerce/admin/categories");
        exit();
    }else{
        header("Location: /ecommerce/admin/categories");
        exit();
    }
    return $response;
});

//Update
$app->get("/ecommerce/admin/categories/{idcategory}", function(Request $request, Response $response, $args){
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    $page = new PageAdmin();

    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $idcategory = $args['idcategory'];

    $cat = new Cat();

    $cat->get($idcategory);

    $page->setTpl("categories-update", array(
        "category" => $cat->getData()
    ));
    return $response;
});
$app->post("/ecommerce/admin/categories/{idcategory}", function(Request $request, Response $response, $args){
    if (!User::verifyLogin()) {
        header("Location: /ecommerce/");
        echo "<script>location.href='/ecommerce/'</script>";
        exit();
    }
    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }

    $idcategory = $args['idcategory'];

    $cat = new Cat();

    $cat->get($idcategory);
    $cat->setData($_POST);
    if($cat->save()){
        header("Location: /ecommerce/admin/categories");
        exit();
    }else{
        header("Location: /ecommerce/admin/categories/$idcategory");
        exit();
    }
    return $response;
});

$app->run();