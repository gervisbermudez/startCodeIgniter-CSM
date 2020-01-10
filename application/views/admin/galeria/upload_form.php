<?php echo $error;?>
<?php echo form_open_multipart($load_to);?>
<?php if (isset($nomultiple)): ?>
	<input id="file-0a" class="file" type="file" name="imagenes[]" data-min-file-count="1">
<?php else: ?>
	<input id="file-0a" class="file" type="file" name="imagenes[]" multiple data-min-file-count="1">
<?php endif ?>
	
</form>