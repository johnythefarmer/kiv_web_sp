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
					case "delete" : delete($request->uzivatel, $id);break;
					case "make-admin" : makeAdmin($request->uzivatel);break;
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

	function delete($uzivatel, $adminId){
		$usMod = new UserModel();
		if($adminId != $uzivatel->id){
			$deleteOk = $usMod->deleteUser($uzivatel->id);
		} else {
			$deleteOk =false;
		}
		
		if($deleteOk){
			$message = "Smazání proběhlo v pořádku";
		}else {
			$message = "Smazání se nepodařilo uskutečnit";
		}
		
		echo json_encode(array("success" => $deleteOk, "message" => $message, "uzivatele" => $usMod->getAllUsersData()));
	}

	function makeAdmin($uzivatel){
		$usMod = new UserModel();
		$actionOk = $usMod->makeAdmin($uzivatel->id);
		
		if($actionOk){
			$message = "Udělení administrátorských práv proběhlo v pořádku";
		}else {
			$message = "Udělení administrátorských práv se nepodařilo uskutečnit";
		}
		
		echo json_encode(array("success" => $actionOk, "message" => $message, "uzivatele" => $usMod->getAllUsersData()));
	}

	function error(){
		echo json_encode(array("success" => false, 'message' => "neoprávněný přístup!!"));
	}

	function select(){
		$usMod = new UserModel();
		echo json_encode(array("success" => true, "uzivatele" => $usMod->getAllUsersData()));
	}

?>