<?php 
    namespace Class\Model;
    use Class\Model;
    use Class\DB\Sql;

    class Product extends Model{

        public static function listAll()
        {

            $sql = new Sql();

            $result = $sql->select("SELECT * FROM tb_products");

            return $result;

        }

        public static function checkList($list)
        {
            foreach ($list as &$row){
                $p = new Product();
                $p->setData($row);
                $row = $p->getData();
            }

            return $list;
        }


        public function save()
        {
            $sql = new Sql();

            $result = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
                ":idproduct" => $this->getidproduct(),
                ":desproduct" => $this->getdesproduct(),
                ":vlprice" => $this->getvlprice(),
                ":vlwidth" => $this->getvlwidth(),
                ":vlheight" => $this->getvlheight(),
                ":vllength" => $this->getvllength(),
                ":vlweight" => $this->getvlweight(),
                ":desurl" => $this->getdesurl(),
            ));

            if($result){
                $this->setData($result[0]);
                $_SESSION['alert'] = "<script>alert('Produto cadastrado com sucesso');</script>";
                return true;
            }
        }

        public function get($idproduct)
        {
            $sql = new Sql();

            $result = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", array(
                ":idproduct" => $idproduct
            ));

            $this->setData($result[0]);
        }

        public function delete()
        {
            $sql = new Sql();

            $result = $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", array(
                ":idproduct" => $this->getidproduct()
            ));

            if($result){
                $_SESSION["alert"] = "<script>alert('Produto deletado com sucesso')</script>";
                return true;
            }else{
                $_SESSION["alert"] = "<script>alert('Erro ao deletar produto')</script>";
                return false;
            }

        }

        public function checkPhoto()
        {
            $filepath = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."ecommerce".DIRECTORY_SEPARATOR."res".DIRECTORY_SEPARATOR."site".DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."products".DIRECTORY_SEPARATOR.$this->getidproduct().".jpg";

            if(file_exists("$filepath")){
                $url = "/ecommerce/res/site/img/products/".$this->getidproduct().".jpg";
            }else{
                $url = "/ecommerce/res/site/img/products/product.jpg";
            }

            return $this->setdesphoto($url);
        }

        public function setPhoto($file)
        {
            //Pegando a extenção do arquivo
            $ext = explode(".", $file['name']);
            //Certificando que será a ultima posição do array(extensão)
            $ext = end($ext);

            //Criando um switch case para verificar a extenção do arquivo corretamente
            switch($ext){
                //Verifica se é JPG ou JPEG
                case "jpg":
                case "jpeg":
                    $image = imagecreatefromjpeg($file['tmp_name']);
                break;

                //Verifica se é GIF
                case "gif":
                    $image = imagecreatefromgif($file['tmp_name']);
                break;

                //Verifica se é PNG
                case "png":
                    $image = imagecreatefrompng($file['tmp_name']);
            
                break;
            }

            $filepath = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."ecommerce".DIRECTORY_SEPARATOR."res".DIRECTORY_SEPARATOR."site".DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."products".DIRECTORY_SEPARATOR.$this->getidproduct().".jpg";

            imagejpeg($image, $filepath);

            imagedestroy($image);

            $this->checkPhoto();
        }

        public function getData()
        {

            $this->checkPhoto();

            $values = parent::getData();

            return $values;
            
        }
    }
?>