<?php

	require_once "Options.class.php";
	require_once "View.class.php";
	require_once "ColorExtractor.class.php";

	class Cset { 

		var $schemes = array();

	    public function __construct() {

	    	$this->options = new Cset_Options();

			$this->schemes = array_merge(
				$this->options->get_schemes(),
				$this->schemes
			);

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

				$styles = "";
				if (file_exists(($current_css_tmpl = $this->options->get_css_templ()))) {
					$styles = file_get_contents($current_css_tmpl);
				}

				$styles = str_replace(
					array_reverse(array_keys($this->schemes[get_theme_mod('color_scheme')])),
					array_reverse($this->schemes[get_theme_mod('color_scheme')]),
					$styles
				);

				$styles = str_replace('$scheme_name ','',$styles);

				$customized_script->set('styles', $styles);
				echo $customized_script->render();				
			}

			if (get_option('dynamic_theme_colors')) {

				$customized_js = new Cset_View("js_schemes_array");
				$all_styles = "";
				$styles = file_get_contents($current_css_tmpl);
				$schemes = array();
				
				foreach ($this->schemes as $name => $colors) {
					$name = 'cset-scheme-'.$name;
					$mod_styles = str_replace(
						array_reverse(array_keys($colors)),
						array_reverse($colors),
						$styles
					);
					$all_styles .= str_replace('$scheme_name','.'.$name,$mod_styles);	
					$schemes[] = $name; 				
				}

				$customized_script->set('styles', $all_styles);
				echo $customized_script->render();

				$customized_js->set('schemes',$schemes);
				echo $customized_js->render();
			}
		}
	} 