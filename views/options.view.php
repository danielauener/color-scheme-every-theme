<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e("Color Scheme every Theme settings", 'cset'); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields('color-scheme-every-theme'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Color Schemes', 'cset') ?></th>
				<td><?php
					if (!$template_exists) : ?>
						<p><?php _e("There is no color scheme generatad for your current theme.", 'cset'); ?></p><?php
					elseif ($colors): ?>
						<textarea rows="10" style="width:100%;">if ( function_exists( 'cset_add_color_scheme' ) ) {
	cset_add_color_scheme( 'scheme-name', array( <?php
							echo "\n";

							foreach (array_unique($colors[0]) as $color):
								echo "\t\t".'\''.$color.'\' => \'#111111\','."\n";
							endforeach; ?>
	) );
}</textarea>
						<p class="description"><?php _e('Add this code to your <b>functions.php</b>, for every color scheme you want to add, and modify the color codes as you whish.', 'cset'); ?></p><br /><br />
						<textarea rows="10" style="width:100%;"><?php
							echo $default_colors; 
						?></textarea>
						<p class="description"><?php _e('These are the default colors, extracted from your current theme.', 'cset'); ?></p><?php
					endif; ?>
				</td>
			</tr>	 
		</table>
		<p class="submit">
			<input type="hidden" name="generate_color_scheme" value="1" />
			<input type="submit" class="button-primary" value="<?php echo ($template_exists) ? _e('Regenerate Color Scheme', 'cset') : _e('Generate Color Scheme', 'cset'); ?>" />
		</p>
	</form>
</div>