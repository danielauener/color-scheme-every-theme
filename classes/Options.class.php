<?php

	require_once "View.class.php";
	require_once "ColorExtractor.class.php";

	class Cset_Options {  
	    
		private $options = array();

		private $options_view = null;

		private $css_extractor = null;


	    public function __construct() { 
	    	add_action('admin_menu',array(&$this,'add_admin_options_menu'));
	    	add_action('admin_init',array(&$this,'register_admin_options'));
	    	
	    	$this->options_view = new Cset_View("options");
	    	$this->css_extractor = new Cset_ColorExtractor();

			add_filter('plugin_action_links_color-scheme-every-theme/color-scheme-every-theme.php',array(&$this,'settings_link'));
	    
			if (isset($_REQUEST['generate_color_scheme']) && $_REQUEST['generate_color_scheme'] == '1') {
				$this->css_extractor->extract_color_scheme();
			}

	    }


		public function settings_link($links) { 
			$settings_link_view = new Cset_View("settings_link");
			array_unshift($links,$settings_link_view->render()); 
			return $links; 
		}


		public function register_admin_options() {
			register_setting('color-scheme-every-theme','gmaps_api_key');
		}


		public function add_admin_options_menu() {
			add_options_page(
				'Color Scheme every Theme',
				'Color Scheme every Theme',
				'manage_options',
				'color-scheme-every-theme',
				array($this,'print_admin_options_page')
			);
		}


		public function print_admin_options_page() {
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.', 'cset'));
			}

			$this->options_view->set('template_exists', $this->css_extractor->color_scheme_exists());
			$this->options_view->set('colors', $this->css_extractor->get_color_scheme());
			$this->options_view->set('default_colors', $this->css_extractor->get_default_scheme());
	        echo $this->options_view->render();
		}
	}