<?php
    Class Login extends Controller{
        protected $canRender;
        protected $success;
        protected $message;
        
        public function __construct(){
            parent::__construct();
            $this->canRender = true;
            
            if($this->logged){
                $error = new Error('Jste již Přihlášen!');
                $error->index();
                $this->canRender = false;
                return false;
            }
            $this->view = new View("login.html");
            if(isset($_POST['nickname'])){
                if($this->checkPost()){
                    $this->view = new View("index_logged.html");
                    $this->success = true;
                    $this->model['trophy'] = new TrophyModel();
                }else {
                    $this->success = false;
                    $this->message = "Zadané špatné přihlašovací údaje";
                }
            }else {
                $this->success = false;
                
//                echo "nic nebylo postnuto";
            }
            
//            $this->view = new RegisterView($success);
        } 
        
        public function index($data = false){
            if($this->canRender){
                if($this->success){
                    $this->view->render(array('isAdmin' => $this->isAdmin,'user' => $this->userData, 'trophies' => $this->model['trophy']->getRecentTrophies()));
//                    $this->view->render(array(),'Registrace proběhla v pořádku, nyní se můžete přihlásit.');
                }else{
                    $this->view->render($this->message);
//                    $this->view->render(array('mo' => $this->model['mo']->getAllMO()));
                }
            }
        }
        
        private function checkPost(){
            $nick = $_POST['nickname'];
            $pwd = $_POST['p'];

            $id = $this->model['user']->checkLogin($nick,$pwd);
            if($id != -1){
                $_SESSION['nick'] = $nick;
                $_SESSION['pwd'] = $pwd;
                $this->logged = true;
                $this->userData = $this->model['user']->getUserData($id);
                $this->isAdmin = $this->model['user']->isAdmin($id);
                return true;
            }
            return false;
        }
    
    }

?>