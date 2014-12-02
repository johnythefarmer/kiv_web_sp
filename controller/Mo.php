<?php
    Class Mo extends Controller{
        
        public function __construct(){
            parent::__construct();
            $this->view = new View("mo_profil.html");
            if($this->logged){
                $this->model['trophy'] = new TrophyModel();
				$this->model['mo'] = new MOModel();
//                $this->model['user'] = new UserModel();
            }
        }
        
        public function index($data = false){
            $this->id($this->userData['moId']);
        }
        
        public function id($id = false){
            if($this->logged){
                $moData = $this->model['mo']->getMOData($id);

                if($moData == null || $id == null){
                     $error = new Error("Tato místní organizace neexistuje");
                    $error->index();
                    return;
                }
                $this->view->render(array('isAdmin' => $this->isAdmin,
                                          'mo' => $moData, 'user' => $this->userData,
                                          'trophy' => $this->model['trophy']->getMOBiggestTrophy($id), 'reviry' => $this->model['mo']->getAllFishgrounds($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
        
        public function trophies($id = false){
            if($this->logged){
                $moData = $this->model['mo']->getMOData($id);

                if($moData == null || $id == false){
                     $error = new Error("Tato místní organizace neexistuje");
                    $error->index();
                    return;
                }
                $this->view->setTemplate("mo_trophies.html");
                
                $this->view->render(array('isAdmin' => $this->isAdmin,
                                          'mo' => $moData,'user' => $this->userData,
                                          'trophies' => $this->model['trophy']->getAllMOTrophies($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
		
		public function fishground($id = false){
            if($this->logged){
                $fsg = $this->model['mo']->getFishgroundData($id);

                if($fsg == null || $id == false){
                     $error = new Error("Tento revír neexistuje");
                    $error->index();
                    return;
                }
                $this->view->setTemplate("fishgr_trophies.html");
                
                $this->view->render(array('isAdmin' => $this->isAdmin,
                                          'revir' => $fsg,'user' => $this->userData,
                                          'trophies' => $this->model['trophy']->getAllFishgroundTrophies($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
    }

?>