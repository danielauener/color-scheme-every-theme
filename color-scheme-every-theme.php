<?php
/*
	Plugin Name: Color Scheme every Theme
	Plugin URI: http://www.danielauener.com/color-scheme-every-theme
	Description: This plugin lets you change the entire color scheme of the current theme via the theme customizer.
	Version: 1.0
	Author: @danielauener
	Author URI: http://www.danielauener.se
	License: GPL2
*/
?>
<?php

	require_once "classes/Cset.class.php";
	include_once "classes/WP_Customize_Color_Scheme_Control.php";
 
 	$cset = new Cset();

	function cset_add_color_scheme($name, $scheme) {
		global $cset;
		$cset->add_color_scheme($name, $scheme);
	}