<?php
    Class Model{
        protected static $pdo;
        
        public function __construct(){
            if(self::$pdo == null){
                try{
                    self::$pdo = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME,DB_USER,DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    
                }catch(Exception $e){
                    echo $e->getMessage();
                }
            }
        }
    }
?>