<?php 

    namespace Class\Model;
    use Class\DB\Sql;
    use Class\Model;
    use Class\Model\User;

    class Cart extends Model{

        const SESSION = "Cart";

        public static function getFromSession()
        {
            $cart = new Cart();
            
            //Se a sessão existir e o id do carrinho for maior que 0
            if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0){
                $cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
            }else{
                //Tentando recuperar o carrinho caso o if for falso
                $cart->getFromSessionId();

                //Verificando se consseguiu carregar o carrinho, se não, criaremos um novo
                if(!(int)$cart->getidcart() > 0){
                    $data = [
                        "dessesionid" => session_id(),
                    ];

                    if(User::checkLogin(false)){

                        $user = User::getFromSession();
                        $data['iduser'] = $user->getiduser();
                    }

                    $cart->setData($data);
                    $cart->save();

                    $cart->setToSession();
                }
            }
            return $cart;
        }

        public function setToSession()
        {
            $_SESSION[Cart::SESSION] = $this->getData();
        }

        public function get(int $idcart)
        {
            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", array(
                ":idcart" => $idcart
            ));
        }

        public function getFromSessionId()
        {
            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :sessionid", array(
                ":sessionid" => session_id()
            ));

            if(count($results) > 0){
                $this->setData($results[0]);
            }
        }

        // Save
        public function save()
        {
            $sql = new Sql();
            $results = $sql->select("CALL sp_carts_save(:idcart, :dessesionid, :iduser, :deszipcode, :vlfreight, :nrdays)", array(
                ":idcart" => $this->getidcart(),
                ":dessesionid" => $this->getdessesionid(),
                ":iduser" => $this->getiduser(),
                ":deszipcode" => $this->getdeszipcode(),
                ":vlfreight" => $this->getvlfreight(),
                ":nrdays" => $this->getnrdays(),
            ));
        
            $this->setData($results[0]);
        }

    }

?>