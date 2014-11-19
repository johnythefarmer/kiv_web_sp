<?php
    class ErrorView extends View{
        
        public function __construct(){
            parent::init_templates();
            $this->template = $this->twig->loadTemplate('error.html');
        }
        
        public function render($data = false){
            echo $this->template->render(array('message'=>$data));
        }
    }

?>