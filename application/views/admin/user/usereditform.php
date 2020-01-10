<form action="">
	<div class="form-group">
		<?php foreach ($userdata[0] as $key => $value): ?>
			<label for="<?php echo $key ?>" class="capitalize"><?php echo $key; ?>:</label>
			<input class="form-control" id="<?php echo $key ?>" placeholder="<?php echo $key ?>" name="<?php echo $key ?>" value="<?php echo $value ?>">
		<?php endforeach ?>
	</div>
</form>