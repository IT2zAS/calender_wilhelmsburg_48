<?php
class connect
    {
        const SERVERNAME = "localhost";
        const USERNAME = 'root';
        const DBNAME = "kalenderwilhelmsburg";
        const PASSWORD = "";
        //Test komment Bashar test extra

        public function dbConnet( ){
            try {
                $conn = new PDO("mysql:host=". self::SERVERNAME.";dbname=".self::DBNAME.";charset=utf8mb4", self::USERNAME);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return  $conn ;

            } catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                exit;
            }
        }




    }
    

