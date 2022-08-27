<?php 

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Class\PageAdmin;
    use Class\Model\User;

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

?>