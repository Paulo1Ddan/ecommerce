<?php 
    namespace Class\DB;

    class Sql{

        const HOSTNAME = '127.0.0.1';
        const USERNAME = "root";
        const PASS = "";
        const DBNAME = 'db_ecommerce';
        
        private $conn;

        public function __construct()
        {
            $this->conn = new \PDO("mysql:host=".Sql::HOSTNAME.";dbname=".Sql::DBNAME, Sql::USERNAME, Sql::PASS);
        }
        
        private function setParams($statement, $params = array())
        {
            foreach ($params as $key => $value) {
                $this->bindParam($statement, $key, $value);
            }
        }

        private function bindParam($statement, $key, $value){
            $statement->bindValue($key, $value);
        }

        public function query($rawQuery, $params = array())
        {
            $stmt = $this->conn->prepare($rawQuery);

            $this->setParams($stmt, $params);

            if($stmt->execute()){
                return true;
            }else{
                return false;
            }

        }

        public function select($rawQuery, $params = array()):array
        {
            $stmt = $this->conn->prepare($rawQuery);
            $this->setParams($stmt, $params);
            if($stmt->execute()){
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }else{
                return false;
            }
            
        }
    }
?>