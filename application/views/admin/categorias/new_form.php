<div class="main">
	<?php echo $header ?>
	<div class="container">
		<div class="row">
			<form action="<?php echo $action; ?>" method="post" id="form" class="col s12 m10 l10">
				<input type="hidden" name="id_form" value="<?php echo element('id', $categoria, ''); ?>">
				<span class="header grey-text text-darken-2">Datos básicos <i class="material-icons left">description</i></span>
				<div class="input-field">
					<label for="nombre" >Nombre:</label>
					<input type="text" id="nombre" name="nombre_form" required="required" value="<?php echo element('nombre', $categoria, ''); ?>">
				</div>
				<div id="introduction" class="section scrollspy">
					<label for="id_cazary" >Descripcion de la categoria:</label>
					<div class="input-field">
						<textarea  id="id_cazary" name="descripcion_form"><?php echo element('description', $categoria, ''); ?></textarea>
					</div>
					<br>
				</div>
				<div class="input-field">
					<select name="tipo_form">
						<option value="" disabled>Selecciona</option>
						<option value="Video" <?php if (element('tipo', $categoria, '')=="Video"): ?>selected<?php endif ?>>Video</option>
						<option value="Evento" <?php if (element('tipo', $categoria, '')=="Evento"): ?>selected<?php endif ?>>Evento</option>
						<option value="Album" <?php if (element('tipo', $categoria, '')=="Album"): ?>selected<?php endif ?>>Album</option>
					</select>
					<label>Tipo de categoria:</label>
				</div>
				<?php if (5 > $this->session->userdata('level')): ?>
				<br>
				Publicar categoria
				<br>
				<div class="input-field">
						<div class="switch">
							<label>
								No publicado
								<input type="checkbox" name="status_form" value="1" <?php if (element('status', $categoria, '')=="1"): ?>
								checked="checked"
							<?php endif ?>>
								<span class="lever"></span>
								Publicado
							</label>
						</div>
					</div>
				<?php endif ?>
				<br><br>
				<div class="input-field" id="buttons">
					<a href="<?php echo base_url('admin/categorias/'); ?>" class="btn red darken-1">Cancelar</a>
					<button type="submit" class="btn btn-primary"><i class="material-icons right">done</i> Guardar</button>	
				</div>
			</form>
		</div>
	</div>
</div>

