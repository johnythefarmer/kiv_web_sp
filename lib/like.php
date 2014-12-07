<?php
	$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
	function autoloader($class) {
        $fileLib = "../lib/$class.php";
        $fileConf = "../config/$class.php";
        $fileModel = "../model/$class.php";
        
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
	require_once "../config/conf.php";
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
		$trophyMod = new TrophyModel();
			if(isset($request->trophyId)){
				$likeOk = $userMod->addLike($id, $request->trophyId);
			if($likeOk){
				header("Content-type:application/json"); 
				echo json_encode(array('likeCount' => count($trophyMod->getAllLikes($request->trophyId))));
			}else{
				echo "Neopravneny pristup!";
			}
		}else{
			echo "Neopravneny pristup!";
		}
		
	}else {
		echo "Neopravneny pristup!";
	}

	

?>