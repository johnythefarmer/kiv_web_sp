<?php
    Class Trophy extends Controller{
        protected $canRender;
        protected $success;
		private $lastId = 0;
        
        public function __construct(){
            parent::__construct();
            
			$this->model['trophy'] = new TrophyModel();
            
            if(!$this->logged){
                $error = new Error('Přístup odepřen');
                $error->index();
                $this->canRender = false;
                return false;
            }
            if(isset($_POST['druh'])){
                $postOk = $this->checkPost();
                if($postOk){
                    $this->canRender = false;
                    $this->view = new View("trophy_profil.html");
					$this->id($this->lastId);
					
                }else {
                    $this->canRender = false;
                }
            }else {
                $this->canRender = true;
                $this->view = new View("trophy_profil.html");
//                $this->model['mo'] = new MOModel();
               // echo "nic nebylo postnuto";
            }
            
//            $this->view = new RegisterView($success);
        } 
        
		public function id($trophyId = false){
			$trophy = $this->model['trophy']->getTrophyById($trophyId);
			
			if($trophy == null){
				$error = new Error("Tento úlovek neexistuje");
                    $error->index();
                    return;
			}else{
				$this->view->render(array('isAdmin' => $this->isAdmin, 'user' => $this->userData, 'trophy' => $trophy, 'likes' => $this->model['trophy']->getAllLikes($trophyId)));
			}
		}
		
        public function index($data = false){
            if($this->canRender){
               	if($this->lastId == 0){
					$this->lastId = $this->model['trophy']->getMostRecentTrophyId();
				}
                
				$this->id($this->lastId);
//                $this->view->render(array('isAdmin' => $this->isAdmin,'user' => $this->userData, 'trophies' => $this->model['trophy']->getRecentTrophies()));
            }
        }
		
		public function post(){
			$this->view->setTemplate("trophy_post.html");
			$this->model['mo'] = new MOModel();
			$this->view->render(array('isAdmin' => $this->isAdmin, 'user' => $this->userData, 'druh' => $this->model['trophy']->getAllDruhy(), 'revir' => $this->model['mo']->getAllFishgrounds()));
		}
        
        private function checkPost(){
            
                $druh = $_POST['druh'];
                $velikost = $_POST['velikost'];
                $vaha = $_POST['vaha'];
                $datum = date('Y-m-d', strtotime($_POST['datum']));
                $revir = $_POST['revir'];
                $img = $this->uploadImage();
                if($img != null){
					$this->lastId = $this->model['trophy']->post($this->userData['id'], $druh, $velikost, $vaha, $datum, $revir, $img);
                    if($this->lastId != 0){
                        return true;
                    }else {
                        unlink('public/img/trophy/'.$img);
                        $error = new Error("Úlovek se nepodařilo vložit.");
                        $error->index();
                        return false;
                    }
                }
            
                
                return false;
//            }
        }
        
        private function uploadImage(){
            $target_dir = "public/img/trophy/";
            
            if(isset($_FILES["file-choose"])){
                $imageFileType = pathinfo(basename($_FILES["file-choose"]["name"]),PATHINFO_EXTENSION);
                do{
                    $target_file = rand(100000000,999999999) . "." . $imageFileType;
                }while(file_exists($target_dir.$target_file));
//                echo $target_file;
                
                $check = @getimagesize($_FILES["file-choose"]["tmp_name"]);
                if($check == false) {
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