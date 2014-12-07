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
            }
        } 
        
		public function id($trophyId = false){
			if(!is_numeric($trophyId)){
				$error = new Error("Neplatné id úlovku");
                    $error->index();
                    return;
			}
			
			$trophy = $this->model['trophy']->getTrophyById($trophyId);
			
			
			if($trophy == null){
				$error = new Error("Tento úlovek neexistuje");
                    $error->index();
                    return;
			}else{
				
				$canEdit = ($this->userData['id'] == $trophy['lovecId']); 
				$this->view->render(array('isAdmin' => $this->isAdmin, 'canEdit' => $canEdit,
										  'user' => $this->userData, 'trophy' => $trophy,
										  'likes' => $this->model['trophy']->getAllLikes($trophyId)));
			}
		}
		
        public function index($data = false){
            if($this->canRender){
               	if($this->lastId == 0){
					$this->lastId = $this->model['trophy']->getMostRecentTrophyId();
				}
                
				$this->id($this->lastId);
            }
        }
		
		public function edit($id){
			if(!is_numeric($id)){
				$error = new Error("Tato stránka neexistuje");
				$error->index();
				return false;
			}
			
			$trophy = $this->model['trophy']->getTrophyData($id);
			if($this->userData['id'] != $trophy['lovecId']){
				$error = new Error('Přístup odepřen');
                $error->index();
				return false;
			}
			
			if(isset($_POST['druhEdit'])){
				$druh = $_POST['druhEdit'];
				$velikost = $_POST['velikostEdit'];
				$vaha = $_POST['vahaEdit'];
				$datum = date('Y-m-d', strtotime($_POST['datumEdit']));
				$revirFullId = explode("-",$_POST['revirEdit']);
				$revir = $revirFullId[0];
				$podrevir = $revirFullId[1];
				if(empty($_FILES['file-chooseEdit']['tmp_name'])){
					
					$updateOk = $this->model['trophy']->editTrophy($id, $druh, $velikost, $vaha, $datum, $revir, $podrevir);
					if($updateOk){
						$this->id($id);
					}else{
						$error = new Error("Nastala chyba při změně údajů ůlovku");
						$error->index();
					}
				} else {
					$img = $this->uploadImageEdit();
					if($img == null){
						return false;
					}
				
					$oldImg = $trophy['img'];
					$updateOk = $this->model['trophy']->editTrophy($id, $druh, $velikost, $vaha, $datum, $revir, $podrevir, $img);
					if($updateOk){
						unlink("public/img/trophy/".$oldImg);
						$this->id($id);
					}else{
						unlink("public/img/trophy/".$img);
						$error = new Error("Nastala chyba při změně údajů");
						$error->index();
					}
				}
			}else {
				$this->view->setTemplate("trophy_edit.html");
				$this->model['mo'] = new MOModel();
				$this->view->render(array('isAdmin' => $this->isAdmin, 'user' => $this->userData,
										  'druh' => $this->model['trophy']->getAllDruhy(),
										  'trophy' => $trophy,
										  'revir' => $this->model['mo']->getAllFishgrounds()));
			}
		}
		
		private function uploadImageEdit(){
			$target_dir = "public/img/trophy/";
            
            if(isset($_FILES["file-chooseEdit"])){
                $imageFileType = pathinfo(basename($_FILES["file-chooseEdit"]["name"]),PATHINFO_EXTENSION);
                do{
                    $target_file = rand(100000000,999999999) . "." . $imageFileType;
                }while(file_exists($target_dir.$target_file));
//                echo $target_file;
                
                $check = @getimagesize($_FILES["file-chooseEdit"]["tmp_name"]);
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
            
            if (@move_uploaded_file($_FILES["file-chooseEdit"]["tmp_name"], $target_dir.$target_file)) {
                return $target_file;
            } else {
                $error = new Error("Soubor se nepodařilo uložit zkuste to znovu později.");
                $error->index();
                return null;
            }
		}
		
		public function post(){
			$this->view->setTemplate("trophy_post.html");
			$this->model['mo'] = new MOModel();
			$this->view->render(array('isAdmin' => $this->isAdmin, 'user' => $this->userData,
									  'druh' => $this->model['trophy']->getAllDruhy(),
									  'revir' => $this->model['mo']->getAllFishgrounds()));
		}
        
        private function checkPost(){
            
                $druh = $_POST['druh'];
                $velikost = $_POST['velikost'];
                $vaha = $_POST['vaha'];
                $datum = date('Y-m-d', strtotime($_POST['datum']));
                $revirFullId = explode("-",$_POST['revir']);
				$revir = $revirFullId[0];
				$podrevir = $revirFullId[1];
                $img = $this->uploadImagePost();
                if($img != null){
					$this->lastId = $this->model['trophy']->post($this->userData['id'],
																 $druh, $velikost, $vaha,
																 $datum, $revir, $podrevir,
																 $img);
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
        
        private function uploadImagePost(){
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