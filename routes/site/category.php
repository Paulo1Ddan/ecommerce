<?php 

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use Class\Page;
    use Class\PageAdmin;
    use Class\Model\User;
    use Class\Model\Cat;
    use Class\Model\Product;

    //Categorias
    $app->get("/ecommerce/category/{idcategory}", function (Request $request, Response $response, $args) {
        $cat = new Cat();

        $idcategory = $args['idcategory'];

        $cat->get($idcategory);

        $page = new Page();

        $page->setTpl("category", array(
            "category" => $cat->getData(),
            "products" => Product::checkList($cat->getProducts())
        ));

        return $response;

    });

?>