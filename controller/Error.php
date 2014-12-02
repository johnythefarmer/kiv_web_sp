<?php
    Class Error extends Controller{
        protected $message;
        public function __construct($message){
//            require('view/ErrorView.php');
            $this->view = new View("error.html");
            $this->message = $message;
        }
        
        public function index($data = false){
            $this->view->render($this->message);
        }
    }

?>