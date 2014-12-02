<?php
    Class Index extends Controller{
        
        public function __construct(){
            parent::__construct();
            if($this->logged){
                $this->model['trophy'] = new TrophyModel();
				$this->view = new View("index_logged.html");
            }else {
				$this->view = new View("index_not_logged.html");
			}
        } 
        
        public function index($data = false){
            if($this->logged){
                $this->view->render(array('isAdmin' => $this->isAdmin,'user' => $this->userData, 'trophies' => $this->model['trophy']->getRecentTrophies()));
            } else {
                $this->view->render(array());
            }
        }
        
        public function logout(){
            if($this->logged){
                $_SESSION = array();
                session_destroy();
                $this->logged = false;
				$this->view->setTemplate("index_not_logged.html");
                $this->view->render(array(), "Byli jste úspěšně odhlášeni");
            }else{
                $error = new Error("Neoprávněný přístup");
                $error->index();
            }
        }
    }

?>