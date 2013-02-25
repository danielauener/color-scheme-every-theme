<?php

	class Cset_View {  
	    
		var $view = "";

		var $vars = array();


		public function __construct($view) {
			if (file_exists(pathinfo(__FILE__, PATHINFO_DIRNAME)."/../views/".$view.".view.php")) {
				$this->view = pathinfo(__FILE__, PATHINFO_DIRNAME)."/../views/".$view.".view.php";
			} else {
				wp_die(__("View ".pathinfo(__FILE__, PATHINFO_DIRNAME)."/../views/".$view.".view.php"." not found", 'wplatlng', 'cset'));
			}
		}


		public function set($name,$value) {
			$this->vars[$name] = $value;
		}


		public function render() {
    		extract($this->vars,EXTR_SKIP);
    		ob_start();
    		include $this->view;
    		return ob_get_clean();
		}		
	}