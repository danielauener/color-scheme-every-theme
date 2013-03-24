<?php

	require_once "View.class.php";
	require_once "ColorExtractor.class.php";

	class Cset_Options {  
	    
		private $plugin_path = "";

		private $options = array();

		private $options_view = null;

		private $css_extractor = null;

		private $current_css_tmpl = "";

		private $current_theme_dir = "";

		private $current_theme = "";


	    public function __construct() { 

	    	$this->plugin_path = "/wp-content/plugins/".plugin_basename(dirname(dirname(__FILE__)));

	    	if ( !( $this->options = get_option( 'cset_schemes' ) ) ) {
	    		$this->options = array();
	    	}

	    	add_action('admin_menu',array(&$this,'add_admin_options_menu'));
	    	add_action('admin_init',array(&$this,'register_admin_options'));

			$uploads = wp_upload_dir( );
			$this->current_theme = wp_get_theme( );
			$this->current_css_tmpl = $uploads['basedir'].'/color-scheme-'.$this->current_theme->Name.'-'.$this->current_theme->Version.'.tss';
	    	$this->current_theme_dir = get_stylesheet_directory();

	    	$this->options_view = new Cset_View("options");
	    	$this->css_extractor = new Cset_ColorExtractor();

			add_filter('plugin_action_links_color-scheme-every-theme/color-scheme-every-theme.php',array(&$this,'settings_link'));

			add_action( 'admin_enqueue_scripts', array( $this,'enqueue_css_js' ) );

			$this->generate_scheme();
			$this->save_schemes();

	    }


	    public function generate_scheme() {
	    	if (isset($_REQUEST['generate_color_scheme'])) {

				if ( !array_key_exists( $this->current_theme->Name, $this->options ) ) {
					$this->options[$this->current_theme->Name] = array();	
				}
				
				$this->options[$this->current_theme->Name][0] = $this->css_extractor->extract_color_scheme( $this->current_theme_dir, $this->current_css_tmpl );
				update_option( "cset_schemes", $this->options);
			
			}
	    }

	    public function save_schemes() {
			if (array_key_exists('schemes',$_REQUEST) && array_key_exists($this->current_theme->Name,$_REQUEST['schemes'])) {
				foreach ($_REQUEST['schemes'][$this->current_theme->Name] as $scheme_idx => $scheme_colors) {
					$scheme_idx++;
					$this->options[$this->current_theme->Name][$scheme_idx] = array();
					foreach ($scheme_colors as $color_idx => $color) {
						$this->options[$this->current_theme->Name][$scheme_idx]["$".$color_idx] = $color;
					}

				}
			}

			if (array_key_exists('delete_schemes',$_REQUEST) && array_key_exists($this->current_theme->Name,$_REQUEST['delete_schemes'])) {
				foreach ($_REQUEST['delete_schemes'][$this->current_theme->Name] as $scheme_idx => $delete) {
					$scheme_idx++;
					if ($delete == 1) {
						unset($this->options[$this->current_theme->Name][$scheme_idx]);
					}
				}
			}

			if ( array_key_exists('new_scheme_changed',$_REQUEST) && $_REQUEST['new_scheme_changed'] && array_key_exists("new_scheme",$_REQUEST) && is_array($_REQUEST["new_scheme"])) {
				$scheme = array();
				foreach ($_REQUEST["new_scheme"] as $color_idx => $color) {
					$scheme["$".$color_idx] = $color;
				}
				$this->options[$this->current_theme->Name][] = $scheme;	
			}

			update_option( "cset_schemes", $this->options);

	    }


	    public function get_css_templ() {
	    	return $this->current_css_tmpl;
	    }

	    
	    public function get_schemes() {
	    	$schemes = array();
	    	if (array_key_exists($this->current_theme->Name,$this->options)) {
		    	foreach ($this->options[$this->current_theme->Name] as $scheme_idx => $scheme) {
		    		$schemes["custom-scheme-"+$scheme_idx] = $scheme;
		    	} 
		    }
	    	return $schemes;
	    }


		public function settings_link($links) { 
			$settings_link_view = new Cset_View("settings_link");
			array_unshift($links,$settings_link_view->render()); 
			return $links; 
		}


		public function enqueue_css_js() {
			wp_enqueue_style('cset-options',site_url().$this->plugin_path.'/views/css/options.css');
			wp_enqueue_script('cset-options',site_url().$this->plugin_path.'/views/js/options.js');
		}
		

		public function register_admin_options() {
			register_setting('cset','default_scheme');
			register_setting('cset','custom_schemes');
			register_setting('cset','dynamic_theme_colors');
		}


		public function add_admin_options_menu() {
			add_options_page(
				'Color Scheme every Theme',
				'Color Scheme every Theme',
				'manage_options',
				'cset',
				array($this,'print_admin_options_page')
			);
		}


		public function print_admin_options_page() {
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.', 'cset'));
			}
			$this->options_view->set('template_exists', array_key_exists( $this->current_theme->Name, $this->options ) );
			$this->options_view->set('colors', $this->options);
			$this->options_view->set('theme', $this->current_theme->Name);
	        echo $this->options_view->render();
		}
	}