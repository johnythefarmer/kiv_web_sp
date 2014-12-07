<?php
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

	header("Content-type:application/json"); 

	function autoloader($class) {
        $fileLib = "../../lib/$class.php";
        $fileConf = "../../config/$class.php";
        $fileModel = "../../model/$class.php";
        
        if(file_exists($fileLib)){
            require_once $fileLib;
        }else if(file_exists($fileConf)){
            require_once $fileConf;
        }else if(file_exists($fileModel)){
            require_once $fileModel;
        }
    }

	$id = -1;
    spl_autoload_register('autoloader');
	require_once "../../config/conf.php";
	$userMod = new UserModel();
	
	if (session_status() == PHP_SESSION_NONE) {
                session_start();
    }

	if(isset($_SESSION["nick"]) && isset($_SESSION["pwd"])){
		$nick = $_SESSION["nick"];
		$pwd = $_SESSION["pwd"];


		$id = $userMod->checkLogin($nick,$pwd);
		$logged = ($id != -1);
	}else {
		$logged = false;
	}

	if($logged){
		if($userMod->isAdmin($id)){
			if(isset($request->method)){
				switch($request->method){
					case "select" : select(); break;
					case "create" : create($request->mo); break;
					case "delete" : delete($request->mo);break;
					default: error(); break;
				}
			}else {
				error();	
			}
		}else {
			error();
		}
	}else {
		error();
	}

	function delete($mo){
		$moMod = new MOModel();
		$deleteOk = $moMod->deleteMO($mo->id);
		
		if($deleteOk){
			$message = "Smazání proběhlo v pořádku";
		}else {
			$message = "Smazání se nepodařilo uskutečnit.\nNejdříve smažte všechny uživatele, kteří do této MO patří.";
		}
		
		echo json_encode(array("success" => $deleteOk, "message" => $message, "mo" => $moMod->getAllMO()));
	}

	function error(){
		echo json_encode(array("succes" => false, 'message' => "neoprávněný přístup!!"));
	}

	function select(){
		$moMod = new MOModel();
		echo json_encode(array("success" => true, "mo" => $moMod->getAllMO()));
	}

	function create($mo){
		$moMod = new MOModel();
		$insertOk = $moMod->createMO($mo->nazev, $mo->web);
		if($insertOk){
			$message = "Vložení proběhlo v pořádku.";
		} else {
			$message = "Vožení se nepodařilo uskutečnit.";
		}
		echo json_encode(array("success" => $insertOk, "message" => $message, "mo" => $moMod->getAllMO()));
	}

?>