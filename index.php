<?php
    require_once 'Twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();

	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader); // takhle je to bez cache

	$template = $twig->loadTemplate('template1.html');
    
    $url = "korenova stranka";
    if (isset($_GET['url'])) {
       $url = $_GET['url'];
    }

    echo $template->render(array('url' => $url, 'title' => 'INDEX'));
?>