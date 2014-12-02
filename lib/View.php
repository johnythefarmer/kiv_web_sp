<?php
    class View{
        protected $loader;
        protected $twig;
        protected $template;

		public function __construct($template){
			$this->init_templates();
            $this->template = $this->twig->loadTemplate($template);
		}
		
        protected function init_templates(){  
            require_once 'Twig/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();
            $this->loader = new Twig_Loader_Filesystem('templates');
	        $this->twig = new Twig_Environment($this->loader);// takhle je to bez cache
        }
        
        public function setTemplate($template){
            $this->template = $this->twig->loadTemplate($template);
        }
        
        public function render($data =false){
			echo $this->template->render(array('data'=>$data));
		}
    
    }
?>