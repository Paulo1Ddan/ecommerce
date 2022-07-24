<?php 
    namespace Class\Model;
    use Class\Model;
    use Class\DB\Sql;

    class Cat extends Model{

        public static function listAll()
        {

            $sql = new Sql();

            $result = $sql->select("SELECT * FROM tb_categories");

            return $result;

        }

        public function save()
        {
            $sql = new Sql();

            $result = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
                ":idcategory" => $this->getidcategory(),
                ":descategory" => $this->getdescategory()
            ));

            if($result){
                $this->setData($result[0]);
                $_SESSION['alert'] = "<script>alert('Categoria cadastrada com sucesso');</script>";
                return true;
            }
        }

        public function get($idcategory)
        {
            $sql = new Sql();

            $result = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
                ":idcategory" => $idcategory
            ));

            $this->setData($result[0]);
        }

        public function delete()
        {
            $sql = new Sql();

            $result = $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", array(
                ":idcategory"=>$this->getidcategory()
            ));

            if($result){
                $_SESSION["alert"] = "<script>alert('Categoria deletada com sucesso')</script>";
                return true;
            }else{
                $_SESSION["alert"] = "<script>alert('Erro ao deletar categoria')</script>";
                return false;
            }
        }

        public function update()
        {
            
        }
    }
?>