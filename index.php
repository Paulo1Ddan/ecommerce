<?php
    session_start();
    //session_destroy();
    require_once 'vendor/autoload.php';

    use Slim\Factory\AppFactory;

    $app = AppFactory::create();

    //Stie
    require_once("routes/site/functions.php");

    //Stie
    require_once("routes/site/site.php");
    
    //Site-category
    require_once("routes/site/category.php");

    //Admin
    require_once("routes/admin/admin.php");
    //Admin-users
    require_once("routes/admin/users.php");
    //Admin-forgot
    require_once("routes/admin/forgot-pass.php");
    //Admmin-category
    require_once("routes/admin/category.php");
    //Admin-products
    require_once("routes/admin/products.php");

    $app->run();
?>