<?php
    require_once 'Twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();

	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader); // takhle je to bez cache

	$template = $twig->loadTemplate('template1.html');
     session_start();
    $url = "korenova stranka";
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
            $imageFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
            $target_file = $target_dir . 'lol' . rand(10,100) . "." . $imageFileType;
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }

            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
              echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    echo $template->render(array('url' => $url, 'title' => 'INDEX', 'debug' => print_r($_SESSION,true)));
?>