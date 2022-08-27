<?php 

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Class\PageAdmin;
    use Class\Model\User;
    use Class\Model\Cat;
    use Class\Model\Product;

    //Get Products
    $app->get("/ecommerce/admin/products", function(Request $request, Response $response){
        if(isset($_SESSION['alert'])){
            echo $_SESSION['alert'];
            unset($_SESSION['alert']);
        }

        if (!User::verifyLogin()) {
            header("Location: /ecommerce/");
            echo "<script>history.back()</script>";
            exit;
        }

        $products = Product::listAll();

        $page = new PageAdmin();
        $page->setTpl('products', array(
            "products" => $products
        ));

        return $response;
    });

    //Get Create
    $app->get("/ecommerce/admin/products/create", function(Request $request, Response $response){
        if(isset($_SESSION['alert'])){
            echo $_SESSION['alert'];
            unset($_SESSION['alert']);
        }

        if (!User::verifyLogin()) {
            header("Location: /ecommerce/");
            echo "<script>history.back()</script>";
            exit;
        }

        $page = new PageAdmin();
        $page->setTpl('products-create');

        return $response;
    });
    $app->post("/ecommerce/admin/products/create", function(Request $request, Response $response){
        $product = new Product();

        $product->setData($_POST);
        if($product->save()) {
            header("Location: /ecommerce/admin/products");
            exit();
        }else{
            header("Location: /ecommerce/admin/products/create");
            exit();
        }
    });

    //Get update product
    $app->get("/ecommerce/admin/products/{idproduct}", function(Request $request, Response $response, $args){
        if(isset($_SESSION['alert'])){
            echo $_SESSION['alert'];
            unset($_SESSION['alert']);
        }
        
        if (!User::verifyLogin()) {
            header("Location: /ecommerce/");
            echo "<script>history.back()</script>";
            exit;
        }

        $idProduct = $args['idproduct'];

        $products = new Product();

        $products->get($idProduct);

        $page = new PageAdmin();
        $page->setTpl("products-update", array(
            "product" => $products->getData()
        ));

        return $response;
    });
    $app->post("/ecommerce/admin/products/{idproduct}", function(Request $request, Response $response, $args){
        if (!User::verifyLogin()) {
            header("Location: /ecommerce/");
            echo "<script>history.back()</script>";
            exit;
        }

        $idProduct = $args['idproduct'];

        $product = new Product();

        $product->get($idProduct);

        $product->setData($_POST);

        
        $product->save();
        
        
        
        if($_FILES['file']['name'] !== "") $product->setPhoto($_FILES['file']);
        
        header("Location: /ecommerce/admin/products");
        exit();

        return $response;
    });

    //Delete products
    $app->get("/ecommerce/admin/products/{idproduct}/delete", function(Request $request, Response $response, $args){
        if (!User::verifyLogin()) {
            header("Location: /ecommerce/");
            echo "<script>history.back()</script>";
            exit;
        }
        $idProduct = $args['idproduct'];

        $product = new Product();
        $product->get($idProduct);

        $product->delete();
        header("Location: /ecommerce/admin/products");
        exit();

        return $response;
    });

?>