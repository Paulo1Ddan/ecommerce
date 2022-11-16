<?php 

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use Class\Page;
    use Class\PageAdmin;
    use Class\Model\User;
    use Class\Model\Cat;
    use Class\Model\Product;

    $app->get("/ecommerce/product/{url}", function(Request $request, Response $response, $args){
        $product = new Product();

        $product->getFromUrl($args['url']);

        $page = new Page();

        $page->setTpl("product-detail", [
            "product" => $product->getData(),
            "categories" => $product->getCategories()
        ]);

        return $response;
    });