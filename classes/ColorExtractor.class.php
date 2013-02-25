<?php

	class Cset_ColorExtractor {

		var $current_css_tmpl = false;

		public function __construct() {
			$uploads = wp_upload_dir( );
			$current_theme = wp_get_theme( );
			$this->current_css_tmpl = $uploads['basedir'].'/color-scheme-'.$current_theme->Name.'-'.$current_theme->Version.'.tss';
		}


		private function rglob($pattern='*', $flags = 0, $path='') {
			$paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
			$files=glob($path.$pattern, $flags);
			foreach ($paths as $path) { $files=array_merge($files,$this->rglob($pattern, $flags, $path)); }
			return $files;
		}


		private function parse_css($css) {
			$results = array();
			preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $css, $matches);
			foreach($matches[0] as $i => $original) {
				foreach(explode(';', $matches[2][$i]) as $attr) {
					if (strlen($attr) > 0) {
						$name = strtok($attr, ":");
						$value = strtok(":");
						if ($value) {
							$results[$matches[1][$i]][trim($name)] = trim($value);
						}
					}
				}	
			}
			return $results;
		}


		public function get_custom_styles() {
			if (file_exists($this->current_css_tmpl)) {
				$styles = file_get_contents($this->current_css_tmpl);
				$styles = preg_replace('/\/\*(.|\\n|\\r)+\*\//',"",$styles);
				return $styles;
			}
			return '';
		}


		public function color_scheme_exists() {
			if (!file_exists($this->current_css_tmpl)) {
				return false;
			}
			return true;
		}


		public function get_color_scheme() {
			if ($this->color_scheme_exists()) {
				$scheme = file_get_contents($this->current_css_tmpl);
				if (preg_match_all('/\$color-[0-9]+/',$scheme, $colors)) {
					return $colors;
				}
			}
			return false;
		}


		public function get_default_scheme() {
			if ($this->color_scheme_exists()) {
				$scheme = file_get_contents($this->current_css_tmpl);
				if (preg_match('/\/\*(.|\\n|\\r)+\*\//',$scheme, $default_colors)) {
					return $default_colors[0];
				}
			}
			return false;
		}


		public function extract_color_scheme() {
			$regex = array(
				"`^([\t\s]+)`ism"=>'',
				"`^\/\*(.+?)\*\/`ism"=>"",
				"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
				"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
				"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
			);
			$replace_colors = array();
			$color_rules = array();
			$default_colors = array();			
			foreach ($this->rglob('*.css',0,get_stylesheet_directory()) as $css_file) {
				$clean_css = preg_replace(array_keys($regex),$regex,file_get_contents($css_file));			
				$clean_css = str_replace(array("\r\n","\r","\n"),"",$clean_css);
				foreach ($this->parse_css($clean_css) as $selector => $rules) {
					foreach ($rules as $property => $value) {
						if (preg_match_all('/#[a-f0-9]{3,6}/i',$value, $colors)) {
							foreach ($colors as $color) {
								$color_id = (isset($replace_colors[$color[0]])) ? $replace_colors[$color[0]] : '$color-'.(count($replace_colors)+1);
								$replace_colors[$color[0]] = $color_id;
								$default_colors[$color_id] = $color[0];
							}
							$color_rules[$property.":".$value][] = $selector;
						} 
						if (preg_match_all('/(rgba|rgb|hsl|hsla)\([0-9\,\.]+?\)/',$value, $colors)) {
							foreach ($colors[0] as $color) {
								$color_id = (isset($replace_colors[$color[0]])) ? $replace_colors[$color[0]] : '$color-'.(count($replace_colors)+1);
								$replace_colors[$color] = $color_id;
								$default_colors[$color_id] = $color;
							}
							$color_rules[$property.":".$value][] = $selector;								
						}
					}
				}
			}

			$default_colors_template = "/**\n * Default colors\n *\n"; 
			foreach ($default_colors as $color_id => $default_color) {
				$default_colors_template .= " * ".$color_id.": ".$default_color.";\n";
			}
			$default_colors_template .=  " */\n\n\n";
			
			$css_template = "";
			foreach ($color_rules as $color_rule => $selectors) {
				$css_template .= implode(",\n",$selectors)."{\n\t".$color_rule.";\n}\n";
			}
			$css_template = str_replace(array_keys($replace_colors),$replace_colors,$css_template);
			file_put_contents($this->current_css_tmpl,$default_colors_template.$css_template);			
		}
	}