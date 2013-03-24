<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e("Color Scheme every Theme settings", 'cset'); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields('cset'); ?>
		<h3><?php _e("Existing schemes"); ?></h3>
		<table class="form-table">			
			<?php if (!$template_exists || count($colors[$theme]) == 0) : ?>
				<tr valign="top">
					<th scope="row"><?php _e('Color Schemes', 'cset') ?></th>
					<td>
						<p><?php _e("There is no color scheme generatad for your current theme.", 'cset'); ?></p>
					</td>
				</tr>
			<?php else: ?>
				<tr valign="top">
					<th scope="row"><?php _e('Color Schemes', 'cset') ?></th>
					<td><?php 
						foreach ($colors[$theme] as $scheme_idx => $theme_colors) : ?>
							<div class="scheme">
								<input type="hidden" class="delete-flag" name="delete_schemes[<?php echo $theme; ?>][<?php echo $scheme_idx-1; ?>]" value="0" />
								<div class="scheme-name">
									<b>Scheme <?php echo ($scheme_idx == 0) ? __('default') : $scheme_idx; ?></b>
								</div>
								<div class="scheme-colors"><?php 
									$width = 100/count($theme_colors);
									$color_idx = 1;
									foreach ($theme_colors as $color) : ?>
										<div style="width:<?php echo $width; ?>%;background:<?php echo $color; ?>;" class="scheme-color-<?php echo $color_idx; ?> scheme-color">
											<?php if ($scheme_idx > 0): ?>
												<input type="hidden" name="schemes[<?php echo $theme; ?>][<?php echo $scheme_idx-1; ?>][color-<?php echo $color_idx; ?>]" value="<?php echo $color; ?>" />
											<?php endif; ?>
										</div><?php 
										$color_idx++;
									endforeach; ?>
								</div><?php
								if ($scheme_idx == 0): ?>
									<p class="description"><?php _e("The default colors, extracted from your current theme."); ?></p><?php
								else: ?>
									<p>
										<a class="color-scheme-embed" href="#" ><?php _e('< embedd in theme >'); ?></a>
										| <a class="color-scheme-delete" href="#" ><?php _e('delete'); ?></a>
									</p>
									<p class="embed">
										<label><?php _e('Add this to the <em>functions.php</em> of your theme:'); ?></label>
										<textarea class="embed-code" rows="10" style="width:100%;">if ( function_exists( 'cset_add_color_scheme' ) ) {
	cset_add_color_scheme( 'cset_scheme-<?php echo $scheme_idx; ?>', array( <?php
		echo "\n";

		foreach ($theme_colors as $color_idx => $color):
			echo "\t\t".'\''.$color_idx.'\' => \''.$color.'\','."\n";
		endforeach; ?>
	) );
}</textarea></p><?php
								endif; ?>
							</div><?php						
						endforeach; ?>
					</td>
				</tr>
			</table>
			<h3><?php _e("Add a new scheme"); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('New color scheme', 'cset') ?></th>
					<td>
						<div id="new-scheme" class="scheme-colors new-scheme">
							<input type="hidden" name="new_scheme_changed" value="0" id="new-scheme-changed" /><?php 
							$width = 100/count($theme_colors);
							$color_idx = 1;
							foreach ($colors[$theme][0] as $color) : ?>
								<div style="width:<?php echo $width; ?>%;background:<?php echo $color; ?>;" id="scheme-color-<?php echo $color_idx; ?>" class="scheme-color">
									<input type="hidden" name="new_scheme[color-<?php echo $color_idx; ?>]" value="<?php echo $color; ?>" />
								</div><?php 
								$color_idx++;
							endforeach; ?>
							<div class="new-color">
								<div class="input">
									<label><?php _e('Change color: '); ?></label><input type="text" id="new-color" class="regular-text" />
								</div>
							</div>
						</div>												
					</td>
				</tr>
			<?php endif; ?>
		</table>
		<h3><?php _e("Options"); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Dynamic theme colors', 'cset') ?></th>
				<td>
					<label for="dynamic_theme_colors">
						<input name="dynamic_theme_colors" type="checkbox" id="dynamic_theme_colors" value="1" <?php checked('1', get_option('dynamic_theme_colors')); ?> />
						<?php _e("Embed css for all color schemes in the current theme, so they can be changed dynamically via javascript.", 'cset'); ?>
					</label>												
				</td>
			</tr>
		</table>		
		<p class="submit">
			<input id="save-color-schemes" type="submit" name="save_color_schemes" class="button-primary" value="<?php _e('Save Schemes'); ?>" />
			<input type="submit" name="generate_color_scheme" class="<?php echo ($template_exists) ? 'button' : 'button-primary'; ?>" value="<?php echo ($template_exists) ? _e('Regenerate Color Scheme', 'cset') : _e('Generate Color Scheme', 'cset'); ?>" />
		</p>
	</form>
</div>