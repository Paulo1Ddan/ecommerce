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

        $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

        $pagination = $cat->getProductsPage($page, 8);

        $pagesite = new Page();

        $pages = [];

        for($i= 1; $i<=$pagination['pages'];$i++){
            array_push($pages, [
                "link" => "/ecommerce/category/$idcategory?page=$i",
                "page" => "$i"
            ]);
        }

        $pagesite->setTpl("category", array(
            "category" => $cat->getData(),
            "products" => $pagination['data'],
            "pages" => $pages,
            "atual" => $page,
            "total" => $pagination['pages'],
            "linknext" => "/ecommerce/category/$idcategory?page=".$page+1,
            "linkprev" => "/ecommerce/category/$idcategory?page=".$page-1,
        ));

        return $response;

    });

?>