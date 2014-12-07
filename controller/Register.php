<?php
    Class Register extends Controller{
        protected $canRender;
        protected $success;
        
        public function __construct(){
            parent::__construct();
            
            
            if($this->logged){
                $error = new Error('Jste již registrován!');
                $error->index();
                $this->canRender = false;
                return false;
            }
            if(isset($_POST['nickname'])){
                $postOk = $this->checkPost();
                if($postOk){
                    $this->canRender = true;
                    $this->view = new View("index_not_logged.html");
                    $this->success = true;
                }else {
                    $this->canRender = false;
                }
            }else {
                $this->canRender = true;
                $this->success = false;
                $this->view = new View("register.html");
                $this->model['mo'] = new MOModel();
			}
        } 
        
        public function index($data = false){
            if($this->canRender){
                if($this->success){
                    $this->view->render(array(),'Registrace proběhla v pořádku, nyní se můžete přihlásit.');
                }else{
                    $this->view->render(array('mo' => $this->model['mo']->getAllMO()));
                }
                
//                $this->view->render(array('isAdmin' => $this->isAdmin,'user' => $this->userData, 'trophies' => $this->model['trophy']->getRecentTrophies()));
            }
        }
        
        private function checkPost(){
            
                $nick = $_POST['nickname'];
                $email = $_POST['email'];
                $firstName = $_POST['first-name'];
                $lastName = $_POST['last-name'];
                $pwd = $_POST['pHash'];
                $web = $_POST['web'];
                $mo = $_POST['mo'];
                $img = $this->uploadImage();
                if($img != null){
                    $registerOk = $this->model['user']->register($nick, $email, $firstName, $lastName, $pwd, $web, $mo, $img);
                    if($registerOk){
                        return true;
                    }else {
                        unlink('public/img/profile-pic/'.$img);
                        $error = new Error("Zadaný uživatel již existuje.");
                        $error->index();
                        return false;
                    }
                }
            
                
                return false;
//            }
        }
        
        private function uploadImage(){
            $target_dir = "public/img/profile-pic/";
            
            $uploadOk = 1;
            if(isset($_FILES["file-choose"])){
                $imageFileType = pathinfo(basename($_FILES["file-choose"]["name"]),PATHINFO_EXTENSION);
                do{
                    $target_file = rand(100000000,999999999) . "." . $imageFileType;
                }while(file_exists($target_dir.$target_file));
//                echo $target_file;
                
                $check = @getimagesize($_FILES["file-choose"]["tmp_name"]);
                if($check !== false) {
                    if($check[0] != $check[1]){
                        $error = new Error("Špatné rozměry obrázku");
                        $error->index();
                        return null;
                    }else {
                        $uploadOk = 1;
                    }

                } else {
                    $error = new Error("Neplatný formát souboru");
                    $error->index();
                    return null;
                }
                
                    
                                                        
            } else {
                $error = new Error("Neočekávaná chyba při uploadu obrázku");
                $error->index();
                return null;
            }
            
            if (@move_uploaded_file($_FILES["file-choose"]["tmp_name"], $target_dir.$target_file)) {
                return $target_file;
            } else {
                $error = new Error("Soubor se nepodařilo uložit zkuste to znovu později.");
                $error->index();
                return null;
            }
        }
    }

?>