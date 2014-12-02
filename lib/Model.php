<?php
    Class Model{
        protected static $pdo;
        
        public function __construct(){
            if(self::$pdo == null){
                try{
                    self::$pdo = new PDO('mysql:host=localhost;dbname=trophy','root','',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    
                }catch(Exception $e){
                    echo $e->getMessage();
                }
            }
        }
    }
?>