<?php 
	$modalid = random_string('alnum', 16);
	$modalid_2 = random_string('alnum', 16);
?>
<div class="main">
	<div class="parallax-container">
		<div class="parallax"><img src="<?php echo base_url('img/profile/Material-Wallpaper.jpg')  ?>"></div>
	<?php echo $header; ?>
	</div>
	<div class="container">
		<div class="row">
			<div class="col s12 ">
				<div class="section">
					Publicar album
					<div class="switch">
						<label>
							No publicado
							<input type="checkbox" class="change_state" name="status" data-url="admin/galeria/fn_ajax_change_state/" 
								data-action-param='{"id":"<?php echo $album[0]['id']; ?>", "table":"album"}' <?php if ( $album[0]['status'] == '1'): ?> checked="checked" <?php endif ?> >
							<span class="lever"></span>
							Publicado
							</label>
						<a class='dropdown-button right' href='#' data-activates='albumoptions'><i class="material-icons">more_vert</i></a>
						<ul id="albumoptions" class='dropdown-content'>
							<li><a href="#">Ver en la web</a></li>
							<li><a href="<?php echo base_url('admin/galeria/editaralbum/'); ?>">Editar</a></li>
							<li><a href="#<?php echo $modalid;?>" class="modal-trigger" data-action-param='{"id":"<?php echo $album[0]['id'] ?>", "table":"album"}' data-url="admin/galeria/fn_ajax_delete_data/" data-url-redirect="admin/galeria/" data-ajax-action="inactive" >Eliminar</a></li>
						</ul>
					</div>	
				</div>
			</div>
			<div class="col s12 ">
				<div class="divider"></div>
				<div class="section">
					<?php echo $album[0]['descripcion'] ?>
				</div>
				<div class="divider"></div>
				<br>
			</div>
			<div class="col s12">
				<div class="items">
				<?php if ($items): ?>
					<?php foreach ($items as $key => $value): ?>
					<?php 
						$itemid = random_string('alnum', 16);
						$ddmid = random_string('alnum', 16);
					?>
					<div class="col m4 s6 l3" id="<?php echo $itemid; ?>">
						<div class="card">
							<div class="card-image">
								<img class="materialboxed" src="<?php echo base_url('img/gallery/'.$album[0]['path'].'/'.$value['nombre']); ?>">
							</div>
							<div class="card-content">
								<span><?php echo $value['nombre'] ?></span>
								<?php if ($this->session->userdata('level') <= 2): ?>
									<a class='dropdown-button right' href='#' data-activates='<?php echo $ddmid ?>'><i class="material-icons">more_vert</i></a>
									<ul id='<?php echo $ddmid  ?>' class='dropdown-content'>
										<li><a href="#<?php echo $modalid_2;?>" class="modal-trigger" data-action-param='{"id":"<?php echo $value['id'] ?>", "table":"album_items"}' data-url="admin/galeria/fn_ajax_delete_data/" data-url="admin/galeria/" data-ajax-action="inactive" data-target-selector="<?php echo "#$itemid"; ?>">Eliminar</a></li>
									</ul>
								<?php endif ?>
							</div>
						</div>
					</div>
					<?php endforeach ?>
				<?php else: ?>
					No hay imagens en este album
				<?php endif ?>
			</div>
					
			</div>
		</div>
	</div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
	<a class="btn-floating btn-large red modal-trigger waves-effect waves-circle waves-light tooltipped" data-position="left" data-delay="50" data-tooltip="Subir imagenes"  href="#modal1">
		<i class="large material-icons">publish</i>
	</a>
</div>
<div id="modal1" class="modal bottom-sheet">
	<div class="modal-content">
		<h4>Subir Imagen</h4>
		<?php echo $form ?>
	</div>
	<div class="modal-footer">
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>

<div id="<?php echo $modalid; ?>" class="modal">
	<div class="modal-content">
		<h4>Eliminar album</h4>
		<p>¿Desea eliminar éste album? Esta accion no podrá deshacerse</p>
	</div>
	<div class="modal-footer">
		<a href="#!"  data-action="acept" class=" modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>

<div id="<?php echo $modalid_2; ?>" class="modal">
	<div class="modal-content">
		<h4>Eliminar imagen</h4>
		<p>¿Desea eliminar ésta imagen? Esta accion no podrá deshacerse</p>
	</div>
	<div class="modal-footer">
		<a href="#!"  data-action="acept" class=" modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>