<?php
    class DataBase{
        private $host = "localhost";
        private $dbname = "medicamentos_db";
        private $username = "root";
        private $password = "";
        private $conn;

        public function connect(){
            $this->conn = null;
            try {
                $this->conn = new PDO(
                    "mysql:host=".$this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4",
                    $this->username,
                    $this->password
                );
                //conf manage of errors
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
            } catch (PDOException $e) {
                echo "Error de conexion: ". $e->getMessage();
            }
            return $this->conn;
        }
    }
?>
