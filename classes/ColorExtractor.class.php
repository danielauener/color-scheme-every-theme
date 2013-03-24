<?php

	class Cset_ColorExtractor {


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


		public function extract_color_scheme( $theme_dir, $css_tmpl ) {
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
			
			foreach ( $this->rglob( '*.css', 0, $theme_dir ) as $css_file ) {
				
				$clean_css = preg_replace( array_keys( $regex ), $regex, file_get_contents($css_file) );			
				$clean_css = str_replace( array( "\r\n", "\r", "\n" ), "", $clean_css );
				
				foreach ( $this->parse_css( $clean_css ) as $selector => $rules ) {
					
					foreach ( $rules as $property => $value ) {
						
						if ( preg_match_all( '/#[a-f0-9]{3,6}/i',$value, $colors ) ) {
							
							foreach ($colors as $color) {
							
								$color_id = (isset($replace_colors[$color[0]])) ? $replace_colors[ $color[0] ] : '$color-'.( count( $replace_colors ) + 1 );
								$replace_colors[$color[0]] = $color_id;
								$default_colors[$color_id] = $color[0];
							
							}
							
							$color_rules[$property.":".$value][] = '$scheme_name '.$selector;
						
						} 
						
						if ( preg_match_all( '/(rgba|rgb|hsl|hsla)\([0-9\,\.]+?\)/', $value, $colors ) ) {
							
							foreach ($colors[0] as $color) {
							
								$color_id = (isset($replace_colors[$color[0]])) ? $replace_colors[$color[0]] : '$color-'.(count($replace_colors)+1);
								$replace_colors[$color] = $color_id;
								$default_colors[$color_id] = $color;
							
							}
							
							$color_rules[$property.":".$value][] = '$scheme_name '.$selector;								
						}

					}
				}
			}
			
			$css_template = "";
			
			foreach ($color_rules as $color_rule => $selectors) {
				$css_template .= implode(",\n",$selectors)."{\n\t".$color_rule.";\n}\n";
			}

			$css_template = str_replace( array_keys( $replace_colors ), $replace_colors, $css_template );
			file_put_contents( $css_tmpl, $css_template );

			return $default_colors;
		}
	}