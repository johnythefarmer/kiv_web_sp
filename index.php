<?php
    function autoloader($class) {
        $fileLib = 'lib/' . $class . '.php';
        $fileCont = 'controller/' . $class . '.php';
        $fileView = 'view/' . $class . '.php';
        $fileConf = 'config/' . $class . '.php';
        $fileModel = 'model/' . $class . '.php';
        
        if(file_exists($fileLib)){
            require_once $fileLib;
        } else if(file_exists($fileCont)){
            require_once $fileCont;
        }else if(file_exists($fileView)){
            require_once $fileView;
        }else if(file_exists($fileConf)){
            require_once $fileConf;
        }else if(file_exists($fileModel)){
            require_once $fileModel;
        }
    }
    session_start();
    $_SESSION["nick"] = "user";
    $_SESSION["pwd"] = "pwd";


    spl_autoload_register('autoloader');
  
    $url = isset($_GET['url'])? $_GET['url'] : 'index';
    $url = rtrim($url, '/');
    $url = explode('/',$url);
    
    $file = 'controller/' . $url[0] . '.php';
    
    if(!file_exists($file)){
        $controller = new Error("Zadaná stránka neexistuje!");
        $controller->index();
        return false;
    }
    
    $controller = new $url[0];
    if(isset($url[2])){
        if(method_exists($controller, $url[1])){
            $controller->$url[1]($url[2]);
        }else {
            $controller = new Error("Zadaná stránka neexistuje");
            $controller->index();
        }
        return false;
    }else if(isset($url[1])){
            if (method_exists($controller, $url[1])){
                $reflection = new ReflectionMethod($controller, $url[1]);
                if($reflection->isPublic()){
                    $controller->$url[1]();
                }else {
                    $controller = new Error("Zadaná stránka neexistuje");
                    $controller->index();
                }
            } else {
                $controller = new Error("Zadaná stránka neexistuje");
                $controller->index();
            }
            return false;

    }
    
    $controller->index();

    /*require_once 'Twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();

	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader); // takhle je to bez cache

	$template = $twig->loadTemplate('template1.html');
     session_start();
    $url = "index samotny";
    if (isset($_GET['url'])) {
       $url = $_GET['url'];
        if($url == 'login'){
          
            $_SESSION['lol'] = 'lol';
        }else if($url == 'logout'){
            $_SESSION = array();
            session_destroy();
        }else if($url == 'upform'){
            $template = $twig->loadTemplate('template2.html');
        }else if($url == 'upload'){
            $target_dir = "uploads/";
            
            $uploadOk = 1;
            if(isset($_FILES["fileToUpload"])){
                $imageFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
                $target_file = $target_dir . 'lol' . rand(10,100) . "." . $imageFileType;
                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = @getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if($check !== false) {
                        echo "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } else {
                        echo "File is not an image.";
                        $uploadOk = 0;
                    }
                }
            } else {
                $uploadOk = 0;
            }

           if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    }

    echo $template->render(array('url' => $url, 'title' => 'INDEX', 'debug' => print_r($_SESSION,true)));*/
?>