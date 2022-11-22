<?php 
    namespace Class\Model;
    //error_reporting(~E_ALL);
    use Class\DB\Sql;
    use Class\Model;
    use Class\Mailer;

    class User extends Model
    {

        const SESSION = "User";
        const KEY = "HcodePhp7_cursos";
        
        public static function getFromSession()
        {
            $user = new User();

            if(isset($_SESSION[User::SESSION]) && $_SESSION[User::SESSION]['iduser'] > 0){

                $user->setData();
            }

            return $user;
        }

        public static function checkLogin($inadmin = true)
        {
            if(!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] || !$_SESSION[User::SESSION]['iduser'] > 0){
                // Não está logado
                return false;
            }else{
                if($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin']){
                    return true;
                }else if($inadmin === false){
                    return true;
                }else{
                    return false;
                }
            }
        }


        public static function login($login, $pass)
        {
            $sql = new Sql();
            $results = $sql->select('SELECT * FROM tb_users WHERE deslogin = :LOGIN', array(
                ":LOGIN" => $login
            ));

            if(count($results) > 0){
                $data = $results[0];
                if(password_verify($pass, $data['despassword'])){
                    $user = new User();
                    $user->setData($data);

                    $_SESSION[User::SESSION] = $user->getData();
                    return true;
                }else{
                    echo "<script>alert('Usuario ou senha incorretas!'); history.back();</script>";
                    return false;
                }
            }else{
                return false;
               
            }
        }
        public static function verifyLogin($inadmin = true)
        {
            if(!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] || !$_SESSION[User::SESSION]['iduser'] > 0 || (bool)$_SESSION[User::SESSION]['inadmin'] !== $inadmin){
                return false;
            }else{
                return true;
            }
        }

        public static function logout()
        {
            unset($_SESSION[User::SESSION]);
            unset($_SESSION['logado']);
            return true;
        }

        public static function listAll()
        {
            $sql = new Sql();

            return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson ORDER BY b.desperson");
        }

        public function save()
        {
            $sql = new Sql();

            if($this->getinadmin()){

                $result = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
                    ":desperson" => $this->getdesperson(),
                    ":deslogin" => $this->getdeslogin(), 
                    ":despassword" => $this->getdespassword(), 
                    ":desemail" => $this->getdesemail(), 
                    ":nrphone" => $this->getnrphone(), 
                    ":inadmin" => $this->getinadmin()
                ));
            }else{
                $result = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
                    ":desperson" => $this->getdesperson(),
                    ":deslogin" => $this->getdeslogin(), 
                    ":despassword" => $this->getdespassword(), 
                    ":desemail" => $this->getdesemail(), 
                    ":nrphone" => $this->getnrphone(), 
                    ":inadmin" => 0
                ));
            }

            if($result){
                $this->setData($result[0]);
                $_SESSION['alert'] = "<script>alert('Usuario cadastrado')</script>";
                return true;
            }else{
                $_SESSION['alert'] = "<script>alert('Erro ao cadastrar usuario')</script>";
                return false;
            }

        }

        public function get($iduser)
        {
            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
                ":iduser" => $iduser
            ));

            return $results[0];

        }

        public function update()
        {
            $sql = new Sql();

            $result = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
                ":iduser" => $this->getiduser(),
                ":desperson" => $this->getdesperson(),
                ":deslogin" => $this->getdeslogin(), 
                ":despassword" => $this->getdespassword(), 
                ":desemail" => $this->getdesemail(), 
                ":nrphone" => $this->getnrphone(), 
                ":inadmin" => $this->getinadmin()
            ));
            if($result){
                $_SESSION['alert'] = "<script>alert('Dados atualizados com sucesso')</script>";
                return true;
            }else{
                $_SESSION['alert'] = "<script>alert('Não foi possivel atualizar os dados')</script>";
                return false;
            }
        }

        public function delete($iduser)
        {
            $sql = new Sql();

            $sql->query("CALL sp_users_delete(:iduser)", array(':iduser' => $iduser));
            $_SESSION["alert"] = "<script>alert('Usuariio excluido com sucesso')</script>";
            return true;


        }

        public static function getForgot($email)
        {

            $sql = new Sql();

            $result = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(':email' => $email));

            if(count($result) === 0){
                $_SESSION['alert'] = "<script>alert('Não foi possivel recuperar a senha')</script>";
                return false;
            }else{
                $data = $result[0];

                $result2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                    ":iduser" => $data['iduser'],
                    ":desip" => $_SERVER['REMOTE_ADDR']
                ));

                if(count($result2) === 0){
                    $_SESSION['alert'] = "<script>alert('Não foi possivel recuperar a senha')</script>";
                    return false;
                }else{
                    $dataRecovery = $result2[0];

                    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
                    $code = openssl_encrypt($dataRecovery['idrecovery'], "aes-256-cbc", User::KEY, 0, $iv);
                    $result = base64_encode($iv.$code);

                    $link = "https://localhost/ecommerce/admin/forgot/reset?code=$result";

                    $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha", "forgot", array(
                        "name" => $data['desperson'],
                        "link" => $link
                    ));

                    if($mailer->send()){
                        return true;
                    }
                    

                    return $data;
                }
            }
        }

        public static function validForgotDecripty($result)
        {
            $result = base64_decode($result);

            $code = mb_substr($result, openssl_cipher_iv_length("aes-256-cbc"), null, "8bit");

            $iv = mb_substr($result, 0, openssl_cipher_iv_length("aes-128-cbc"), "8bit");

            $idrecovery = openssl_decrypt($code, "aes-256-cbc", User::KEY, 0, $iv);

            $sql = new Sql();

            $results = $sql->select("
                SELECT * FROM tb_userspasswordsrecoveries a
                INNER JOIN tb_users b USING(iduser)
                INNER JOIN tb_persons c USING (idperson)
                WHERE
                a.idrecovery = :code
                AND 
                a.dtrecovery IS NULL
                AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()
            ", array(
                ":code" => $idrecovery
            ));

            if(count($results) === 0){
                $_SESSION['alert'] = "<script>alert('Não foi possivel recuperar a senha')</script>";
                return false;
            }else{
                return $results[0];
            }
        }

        public static function setForgotUsed($idrecovery)
        {
            $sql = new Sql();

            $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
                ":idrecovery" => $idrecovery
            ));

        }

        public function setPassword($password, $iduser)
        {
            $sql = new Sql();
            $results = $sql->query("UPDATE tb_users SET despassword = :despassword WHERE iduser = :iduser", array(
                ":despassword" => $password,
                ":iduser" => $iduser
            ));

            

            if($results){
                return true;
            }else{
                return false;
            }
        }
    }
?>