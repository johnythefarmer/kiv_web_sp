<?php
    class Controller{
        protected $view;
        protected $model;
        protected $logged = false;
        protected $isAdmin = false;
        protected $userData;
        
        public function __construct(){
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $this->model['user'] = new UserModel();
            if(isset($_SESSION["nick"]) && isset($_SESSION["pwd"])){
                $nick = $_SESSION["nick"];
                $pwd = $_SESSION["pwd"];
                
                
                $id = $this->model['user']->checkLogin($nick,$pwd);
                $this->logged = ($id != -1);
            }else {
                $this->logged = false;
            }
            
            if($this->logged){
                $this->userData = $this->model['user']->getUserData($id);
                $this->isAdmin = $this->model['user']->isAdmin($id);
            }
        }    
        
        public function index(){
            $error = new Error("Tato stránka neexistuje");
			$error->index();
        }

    }
?>