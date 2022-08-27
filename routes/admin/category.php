<?php 

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Class\PageAdmin;
    use Class\Model\User;
    use Class\Model\Cat;

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
?>