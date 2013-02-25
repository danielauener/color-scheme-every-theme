<?php

	require_once "Options.class.php";
	require_once "View.class.php";
	require_once "ColorExtractor.class.php";

	class Cset { 

		var $schemes = array();

	    public function __construct() {

	    	$this->options = new Cset_Options();

			add_action( 'customize_register', array($this,'customize_register') );	
			add_action( 'wp_head', array($this,'customize_css' ) );    	
	    
	    }


		public function init() {
			
			// load languages
			$plugin_dir = "color-scheme-every-theme/languages/";
			load_plugin_textdomain('cset',false,$plugin_dir);
		}


		public function add_color_scheme($name, $scheme) {
			$this->schemes[$name] = $scheme;
		}		


		public function customize_register( $wp_customize ) {
			$wp_customize->add_setting( 'color_scheme' , array(
			    'default'     => '',
			    'transport'   => 'refresh',
			) );
			$wp_customize->add_section( 'color-schemes' , array(
				'title' => __( 'Color Schemes' , 'cset'),
				'priority' => 0,
			) );
			$wp_customize->add_control( new WP_Customize_Color_Scheme_Control( $wp_customize, 'color_scheme', array(
				'label' => __( 'Color Schemes' , 'cset'),
				'section' => 'color-schemes',
				'settings' => 'color_scheme',
				'schemes' => $this->schemes
			) ) );			
		}		


		public function customize_css() {
			if (isset($this->schemes[get_theme_mod('color_scheme')])) {
				$customized_script = new Cset_View("custom_styles");
				$css_extractor = new Cset_ColorExtractor();
				$styles = $css_extractor->get_custom_styles();
				$styles = str_replace(
					array_reverse(array_keys($this->schemes[get_theme_mod('color_scheme')])),
					array_reverse($this->schemes[get_theme_mod('color_scheme')]),
					$styles
				);
				$customized_script->set('styles', $styles);
				echo $customized_script->render();				
			}
		}
	} 