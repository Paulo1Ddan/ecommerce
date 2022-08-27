<?php 

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use Class\Page;
    use Class\PageAdmin;
    use Class\Model\User;
    use Class\Model\Cat;

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
?>