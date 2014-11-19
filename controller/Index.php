<?php
    Class Index extends Controller{
        private $trophyMod;
        
        public function __construct(){
            parent::__construct();
            $this->view = new IndexView($this->logged);
            if($this->logged){
                $this->trophyMod = new TrophyModel();
            }
        }
        
        public function index($data = false){
            $this->view->render(array('uData' => $this->userData, 'trophies' => $this->trophyMod->getRecentTrophies()));
        }
        
        private function lolo(){
            $this->view->render(false);
        }
    }

?>