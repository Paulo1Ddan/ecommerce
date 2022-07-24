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
                Cat::updateFile();
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
                Cat::updateFile();
                return true;
            }else{
                $_SESSION["alert"] = "<script>alert('Erro ao deletar categoria')</script>";
                return false;
            }

        }

        public static function updateFile()
        {
            $categories = Cat::listAll();

            $html = [];

            foreach($categories as $row){
                array_push($html, '<li><a href="/ecommerce/category/'. $row['idcategory'] .'">'. $row['descategory'].'</a></li>');

                file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."ecommerce".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."site".DIRECTORY_SEPARATOR."categories-menu.html",implode("", $html));
            }
        }
    }
?>