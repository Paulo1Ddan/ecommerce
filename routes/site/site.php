<?php 
    require_once 'vendor/autoload.php';

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use Class\Page;
    use Class\PageAdmin;
    use Class\Model\User;
    use Class\Model\Cat;
    use Class\Model\Product;

    //Site
    //Home page - GET
    $app->get('/ecommerce/', function (Request $request, Response $response, $args) {

        $products = Product::listAll();

        $page = new Page();

        $page->setTpl('index',["products" => Product::checkList($products)]);
        return $response;
    });

?>