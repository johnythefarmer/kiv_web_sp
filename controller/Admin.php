<?php
	class Admin extends Controller{
		private $canRender = false;
	
		public function __construct(){
			parent::__construct();
			
			if(!$this->logged || !$this->isAdmin){
				$error = new Error("Zadaná stránka neexistuje");
				$error->index();
				return false;
			}
			
			$this->canRender = true;
			$this->View = new View("admin.html");
		}
		
		public function index($data = false){
			if($this->canRender){
				$error = new Error("Zadaná stránka neexistuje");
				$error->index();
				return false;
			}
		}
		
		public function mo($data = false){
			if($this->canRender){
				if($data != false){
					$error = new Error("Zadaná stránka neexistuje");
					$error->index();
					return false;
				}else {
					$this->View->render(array('user' => $this->userData, 'isAdmin' => $this->isAdmin, 'page' => "mo"));
				}
			}
		}
		
		public function trophies($data = false){
			if($this->canRender){
				if($data != false){
					$error = new Error("Zadaná stránka neexistuje");
					$error->index();
					return false;
				}else {
					$this->View->render(array('user' => $this->userData, 'isAdmin' => $this->isAdmin, 'page' => "trophies"));
				}
			}
		}
		
		public function users($data = false){
			if($this->canRender){
				if($data != false){
					$error = new Error("Zadaná stránka neexistuje");
					$error->index();
					return false;
				}else {
					$this->View->render(array('user' => $this->userData, 'isAdmin' => $this->isAdmin, 'page' => "users"));
				}
			}
		}

		public function rivers($data = false){
			if($this->canRender){
				if($data != false){
					$error = new Error("Zadaná stránka neexistuje");
					$error->index();
					return false;
				}else {
					$this->View->render(array('user' => $this->userData, 'isAdmin' => $this->isAdmin, 'page' => "rivers"));
				}
			}
		}
		
		public function fsg($data = false){
			if($this->canRender){
				if($data != false){
					$error = new Error("Zadaná stránka neexistuje");
					$error->index();
					return false;
				}else {
					$this->View->render(array('user' => $this->userData, 'isAdmin' => $this->isAdmin, 'page' => "fsg"));
				}
			}
		}
		
		public function species($data = false){
			if($this->canRender){
				if($data != false){
					$error = new Error("Zadaná stránka neexistuje");
					$error->index();
					return false;
				}else {
					$this->View->render(array('user' => $this->userData, 'isAdmin' => $this->isAdmin, 'page' => "species"));
				}
			}
		}
	}
	
?>