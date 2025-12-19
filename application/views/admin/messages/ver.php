<div class="main">
	<?php echo $header; ?>
	<div class="container">
		<div class="row">
			<div class="col s12">
				<?php if (isset($mensajes)): ?>
				<?php foreach ($mensajes as $array): ?>
				<div class="card" style="overflow: hidden;">
					<div class="card-content">
						<span class="card-title activator grey-text text-darken-4"><?php echo $array['nombre'] ?></h3></span>
						<a href="#" data-activates='options' class="dropdown-button right"><i class="material-icons">more_vert</i></a>
						<?php echo $options ?>
						<div><b>Email:</b> <a href="mailto:<?php echo $array['email'] ?>"><?php echo $array['email'] ?></a></div>
						<div><b>Telefono:</b> <?php echo $array['telefono'] ?></div>
						<div><b>Fecha:</b> <?php echo $array['fecha'] ?></div>
						
					</div><div class="card-action">
					<?php echo $array['mensaje'] ?>
				</div>
			</div>
			<?php endforeach ?>
			<?php else: ?>
			El mensaje al que intenta acceder no existe :(
			<?php endif ?>
		</div>
	</div>
</div>
</div>
<div id="modal1" class="modal">
<div class="modal-content">
	<h4>Eliminar Mensaje</h4>
	<p>¿Desea eliminar éste Mensaje? Esta accion no podrá deshacerse</p>
</div>
<div class="modal-footer">
	<a href="<?php echo $base_url ?>index.php/Admin/Mensajes/eliminar/<?php echo $array['id']; ?>" class=" modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
	<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
</div>
</div>