<?php
    class ProfileView extends View{
        
        public function __construct(){
            parent::init_templates();
            $this->template = $this->twig->loadTemplate('profil.html');
        }
        
        public function render($data = false){
//            print_r($data);
            echo $this->template->render(array('data'=>$data));
        }
    }

?>