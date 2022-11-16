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

        public function getProducts($related = true)
        {
            $sql = new Sql();
            if($related){
                return $sql->select("SELECT * FROM tb_products WHERE idproduct IN(
                  SELECT a.idproduct FROM tb_products a INNER JOIN tb_productscategories b on a.idproduct = b.idproduct WHERE b.idcategory = :idcategory
                )", array(
                    ":idcategory" => $this->getidcategory()
                ));
            }else{
                return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN(
                  SELECT a.idproduct FROM tb_products a INNER JOIN tb_productscategories b on a.idproduct = b.idproduct WHERE b.idcategory = :idcategory
                )", array(
                    ":idcategory" => $this->getidcategory()
                ));
            }
        }

        public function getProductsPage($page = 1, $itens = 2)
        {
            $start = ($page-1)*$itens;

            $sql = new Sql();
            $resultProduct = $sql->select("SELECT * FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct INNER JOIN tb_categories c ON c.idcategory = b.idcategory WHERE c.idcategory = :idcategory LIMIT $start,$itens", array(
                ":idcategory" => $this->getidcategory()
            ));
            $resultTotal = $sql->select("SELECT COUNT(*) AS nrtotal FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct INNER JOIN tb_categories c ON c.idcategory = b.idcategory WHERE c.idcategory = :idcategory", array(
                ":idcategory" => $this->getidcategory()
            ));

            return array(
                "data" => Product::checkList($resultProduct),
                "total" => $resultTotal[0]['nrtotal'],
                "pages" => ceil((int)$resultTotal[0]['nrtotal']/$itens)
            );
        }

        public function addProduct(Product $product)
        {
            $sql = new Sql();
            $sql->query("INSERT INTO tb_productscategories(idcategory, idproduct) VALUES(:idc, :idp)", array(
                ":idc" => $this->getidcategory(),
                ":idp" => $product->getidproduct(),
            ));
        }

        public function removeProduct(Product $product)
        {
            $sql = new Sql();
            $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idc AND idproduct = :idp", array(
                ":idc" => $this->getidcategory(),
                ":idp" => $product->getidproduct(),
            ));
        }
    }
?>