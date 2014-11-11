<?php
    Class Index extends Controller{
        public function __construct(){
            require('view/IndexView.php');
            $this->view = new IndexView(true);
        }
        
        public function index($data = false){
            $this->view->render($data);
        }
    }

?>