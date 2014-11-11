<?php
    class IndexView extends View{
        protected $logged;
        
        public function __construct($logged){
            $this->logged = $logged;
            parent::init_templates();
            if($logged){
                $this->template = $this->twig->loadTemplate('template2.html');
            }else {
                $this->template = $this->twig->loadTemplate('template1.html');
            }
        }
        
        public function render($data = false){
            echo $this->template->render(array('url' => 'index', 'title' => 'index', 'debug' => $data));
        }
    }

?>