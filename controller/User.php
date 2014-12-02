<?php
    Class User extends Controller{
        
        public function __construct(){
            parent::__construct();
            $this->view = new View("profil.html");
            if($this->logged){
                $this->model['trophy'] = new TrophyModel();
//                $this->model['user'] = new UserModel();
            }
        }
        
        public function index($data = false){
            $this->id($this->userData['id']);
        }
        
        public function id($id = false){
            if($this->logged){
                $userData = $this->model['user']->getUserData($id);
//                var_dump($userData);
                if($userData == null || $id == null){
                     $error = new Error("Tento uživatel neexistuje");
                    $error->index();
                    return;
                }
                $canEdit = ($id == $this->userData['id']);
                $this->view->render(array('canEdit' => $canEdit, 'isAdmin' => $this->isAdmin,
                                          'uData' => $userData,'user' => $this->userData,
                                          'trophy' => $this->model['trophy']->getUsersBiggestTrophy($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
        
        public function trophies($id = false){
            if($this->logged){
                $userData = $this->model['user']->getUserData($id);
//                var_dump($userData);
                if($userData == null || $id == false){
                     $error = new Error("Tento uživatel neexistuje");
                    $error->index();
                    return;
                }
                $this->view->setTemplate("user_trophies.html");
                
                $this->view->render(array('isAdmin' => $this->isAdmin,
                                          'uData' => $userData,'user' => $this->userData,
                                          'trophies' => $this->model['trophy']->getAllUsersTrophies($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
    }

?>