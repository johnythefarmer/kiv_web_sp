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
				
				if(!is_numeric($id)){
					$error = new Error("Neplatné ID Místní organizace");
                	$error->index();
                	return;
				}
				
                $this->view->render(array('isAdmin' => $this->isAdmin,
                                          'mo' => $moData, 'user' => $this->userData,
                                          'trophy' => $this->model['trophy']->getMOBiggestTrophy($id),
										  'reviry' => $this->model['mo']->getAllFishgrounds($id)));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
		
		public function edit($id = false){
            if($this->logged && $this->isAdmin){
                $moData = $this->model['mo']->getMOData($id);

                if($moData == null || $id == null){
                     $error = new Error("Tato místní organizace neexistuje");
                    $error->index();
                    return;
                }
				
				if(!is_numeric($id)){
					$error = new Error("Neplatné ID Místní organizace");
                	$error->index();
                	return;
				}
				
				if(isset($_POST['nazevEdit'])){
					$nazev = $_POST['nazevEdit'];
					$web = $_POST['webEdit'];
					
					
					if(empty($_FILES['file-chooseEdit']['tmp_name'])){
						$updateOk = $this->model['mo']->editMO($id, $nazev, $web);
						if($updateOk){
							$this->id($id);
						}else{
							$error = new Error("Nastala chyba při změně údajů");
							$error->index();
						}
					} else {
						$img = $this->uploadImageEdit();
						if($img == null){
							return false;
						}

						$oldImg = $moData['img'];
						$updateOk = $this->model['mo']->editMO($id, $nazev, $web, $img);
						if($updateOk){
							$this->id($id);
						}else{
							unlink("public/img/mo/".$img);
							$error = new Error("Nastala chyba při změně údajů");
							$error->index();
						}
					}
				}else {
					$this->view->setTemplate("mo_edit.html");
					$this->model['mo'] = new MOModel();
					$this->view->render(array('mo' => $moData,
											  'isAdmin' => $this->isAdmin, 'user' => $this->userData));
				}
               
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
				
				if(!is_numeric($id)){
					$error = new Error("Neplatné ID Místní organizace");
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
		
		private function uploadImageEdit(){
            $target_dir = "public/img/mo/";
            
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
		
		public function fishground($id = false){
            if($this->logged){
                $idArray = explode("-", $id);
				
				

                if($id == false){
                     $error = new Error("Tento revír neexistuje");
                    $error->index();
                    return;
                }
								
				
				if($id == null){
					$error = new Error("Tento revír neexistuje");
                    $error->index();
                    return;
				}
				
				if(!is_numeric($idArray[0]) || !is_numeric($idArray[1])){
					$error = new Error("Neplatné ID revíru");
                	$error->index();
                	return;
				}
				
				
				$fsg = $this->model['mo']->getFishgroundData($idArray[0], $idArray[1]);
                
				if($fsg == null){
					$error = new Error("Tento revír neexistuje");
					$error->index();
					return;
				}
				
				$this->view->setTemplate("fishgr_trophies.html");
                
                $this->view->render(array('isAdmin' => $this->isAdmin,
                                          'revir' => $fsg,'user' => $this->userData,
                                          'trophies' => $this->model['trophy']->getAllFishgroundTrophies($idArray[0], $idArray[1])));
            } else {
                $error = new Error("Přístup odepřen");
                $error->index();
            }
        }
    }

?>