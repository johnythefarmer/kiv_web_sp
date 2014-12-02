<?php
    Class Top extends Controller{
        
        public function __construct(){
            parent::__construct();
            if($this->logged){
                $this->model['trophy'] = new TrophyModel();
				$this->view = new View("top.html");
            }else {
				$this->view = new View("index_not_logged.html");
			}
        } 
        
        public function index($data = false){
            if($this->logged){
                $this->view->render(array('isAdmin' => $this->isAdmin,'user' => $this->userData, 'trophies' => $this->model['trophy']->getRecentTrophies()));
            } else {
                $error = new Error("Přístup odepřen");
				$error->index();
            }
        }
    }

?>