<?php
    Class Index extends Controller{
        
        public function __construct(){
            parent::__construct();
            $this->view = new IndexView($this->logged);
            if($this->logged){
                $this->model['trophy'] = new TrophyModel();
            }
        }
        
        public function index($data = false){
            if($this->logged){
                $this->view->render(array('isAdmin' => $this->isAdmin,'uData' => $this->userData, 'trophies' => $this->model['trophy']->getRecentTrophies()));
            } else {
                $this->view->render();
            }
        }
    }

?>