<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Class\PageAdmin;
use Class\Model\User;

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

?>