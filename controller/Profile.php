<?php
    Class Profile extends Controller{
        
        public function __construct(){
            parent::__construct();
            $this->view = new ProfileView($this->logged);
            if($this->logged){
                $this->model['trophy'] = new TrophyModel();
                $this->model['user'] = new UserModel();
            }
        }
        
        public function index($data = false){
            if($this->logged){
                $this->view->render(array('canEdit' => true,'isAdmin' => $this->isAdmin,
                                          'uData' => $this->userData,
                                          'trophy' => $this->model['trophy']->getUsersBiggestTrophy($this->userData["id"])));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
        
        public function id($id){
            if($this->logged){
                $userData = $this->model['user']->getUserData($id);
//                var_dump($userData);
                if($userData == null){
                     $error = new Error("Tento uživatel neexistuje");
                    $error->index();
                    return;
                }
                $canEdit = ($id == $this->userData['id']);
                echo ($id == $this->userData['id']);
                $this->view->render(array('canEdit' => $canEdit, 'isAdmin' => $this->isAdmin,
                                          'uData' => $userData,
                                          'trophy' => $this->model['trophy']->getUsersBiggestTrophy($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
    }

?>