<?php
    class Controller{
        protected $view;
        protected $model;
        protected $logged;
        protected $userData;
        
        public function __construct(){
            @session_start();
            if(isset($_SESSION["nick"]) && isset($_SESSION["pwd"])){
                $nick = $_SESSION["nick"];
                $pwd = $_SESSION["pwd"];
                
                $uMod = new UserModel();
                
                $this->logged = $uMod->isLogged($nick,$pwd);
            }else {
                $this->logged = false;
            }
            
            if($this->logged){
                $this->userData = $uMod->getUserData();
            }
        }    
        
        public function index(){
            
        }

    }
?>