<?php
    abstract class View{
        protected $loader;
        protected $twig;
        protected $template;
        
        protected function init_templates(){  
            require_once 'Twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();
            $this->loader = new Twig_Loader_Filesystem('templates');
	        $this->twig = new Twig_Environment($this->loader);// takhle je to bez cache
        }
        
        public abstract function render($data =false);
    
    }
?>