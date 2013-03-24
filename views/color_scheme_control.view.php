<label><?php
	foreach ($schemes as $name => $scheme) : ?>
		<input type="radio" name="color_scheme" <?php echo $link; ?> value="<?php echo $name; ?>" /><?php echo $name; ?>
		<div style="height:50px;margin:10px 0;position:relative;"><?php
			$color_count = count($scheme);
			foreach ($scheme as $color) : ?>
				<div style="height:50px;width:<?php echo (100/$color_count); ?>%;background-color:<?php echo $color; ?>;float:left;"><!-- --></div><?php
			endforeach; ?>
		</div><?php
	endforeach ?>
</label>