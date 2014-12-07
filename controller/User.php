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
				if(!is_numeric($id)){
					$error = new Error("Neplatné ID uživatele");
					$error->index();
					return;
				}
				
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
				
				if(!is_numeric($id)){
					$error = new Error("Neplatné ID uživatele");
                	$error->index();
                	return;
				}
				
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
		
		public function edit($id = false){
			if($this->logged && $id == false){
				if(isset($_POST['emailEdit'])){
//					echo "postnuto";
					$email = $_POST['emailEdit'];
					$first_name = $_POST['first-nameEdit'];
					$last_name = $_POST['last-nameEdit'];
					$web = $_POST['webEdit'];
					$mo_id = $_POST['moEdit'];
					
					
					if(empty($_FILES['file-chooseEdit']['tmp_name'])){
						$updateOk = $this->model['user']->editUser($this->userData['id'], $email,
																   $first_name, $last_name, $web, $mo_id);
						if($updateOk){
							$this->id($this->userData['id']);
						}else{
							$error = new Error("Nastala chyba při změně údajů");
							$error->index();
						}
					} else {
						$img = $this->uploadImageEdit();
						if($img == null){
							return false;
						}

						$oldImg = $this->userData['img'];
						$updateOk = $this->model['user']->editUser($this->userData['id'], $email,
																   $first_name, $last_name, $web,
																   $mo_id, $img);
						if($updateOk){
							unlink("public/img/profile-pic/".$oldImg);
							$this->id($this->userData['id']);
						}else{
							unlink("public/img/profile-pic/".$img);
							$error = new Error("Nastala chyba při změně údajů");
							$error->index();
						}
					}
				}else {
					$this->view->setTemplate("user_edit.html");
					$this->model['mo'] = new MOModel();
					$this->view->render(array('mo' => $this->model['mo']->getAllMO(),
											  'isAdmin' => $this->isAdmin, 'user' => $this->userData));
				}
			} else {
				$error = new Error("Přístup odepřen");
				$error->index();
			}
		}
		
		private function uploadImageEdit(){
            $target_dir = "public/img/profile-pic/";
            
            $uploadOk = 1;
            if(isset($_FILES["file-chooseEdit"])){
                $imageFileType = pathinfo(basename($_FILES["file-chooseEdit"]["name"]),PATHINFO_EXTENSION);
                do{
                    $target_file = rand(100000000,999999999) . "." . $imageFileType;
                }while(file_exists($target_dir.$target_file));
//                echo $target_file;
                
                $check = @getimagesize($_FILES["file-chooseEdit"]["tmp_name"]);
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
            
            if (@move_uploaded_file($_FILES["file-chooseEdit"]["tmp_name"], $target_dir.$target_file)) {
                return $target_file;
            } else {
                $error = new Error("Soubor se nepodařilo uložit zkuste to znovu později.");
                $error->index();
                return null;
            }
        }
    }

?>