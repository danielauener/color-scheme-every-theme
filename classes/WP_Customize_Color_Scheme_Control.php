<?php 

	require_once "View.class.php";

	add_action( 'customize_register', 'cset_customize_register', 0 );

	function cset_customize_register($wp_customize) {
		class WP_Customize_Color_Scheme_Control extends WP_Customize_Control {

			public $type = 'color-scheme';

			public $schemes = array();
			
			public function __construct( $manager, $id, $args = array() ) {
				parent::__construct( $manager, $id, $args );
				if (isset($args['schemes'])) {
					$this->schemes = $args['schemes'];
				}
			}

			public function render_content() {
				$control = new Cset_View("color_scheme_control");
				$control->set('schemes',$this->schemes);
				$control->set('link',$this->get_link());
				echo $control->render();
			}
		}
	}