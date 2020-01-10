<div class="main">
	<?php echo $header; ?>
	<div class="container">
		<div class="row">
			<div class="col s12">
				<?php
						$modalid = random_string('alnum', 16);
				?>
		<?php if ($categorias): ?>
				<ul class="collection">
					
					<?php foreach ($categorias as $key => $value): ?>
					<?php 
						$itemid = random_string('alnum', 16);
						$ddmid = random_string('alnum', 16);
					?>
					<li class="collection-item" id="<?php echo $itemid; ?>"><div><?php echo $value['nombre'] ?><a href="#!" class="secondary-content"><i class="material-icons">send</i></a>
					<a class='dropdown-button secondary-content' href='#' data-activates='<?php echo $ddmid ?>'><i class="material-icons">more_vert</i></a>
					<ul id='<?php echo $ddmid ?>' class='dropdown-content'>
						<li><a href="#!">Ver en la web</a></li>
						<?php if (4 > $this->session->userdata('level')): ?>
						<li><a href="<?php echo base_url('admin/categorias/editar/'.$value['id']); ?>">Editar</a></li>
						<?php endif ?>
						<?php if (3 > $this->session->userdata('level')): ?>
						<li><a href="#<?php echo $modalid;?>" class="modal-trigger" data-action-param='{"id":"<?php echo $value['id'] ?>", "table":"categorias"}' data-url="admin/categorias/fn_ajax_delete_data/" data-ajax-action="inactive" data-target-selector="<?php echo "#$itemid"; ?>">Eliminar</a></li>
						<?php endif ?>
					</ul></div>
				</li>
				<?php endforeach ?>
			</ul>
		<?php else: ?>
		No hay categorias aun
		<?php endif ?>
		</div>
	</div>
</div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
	<a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear categoria" href="<?php echo base_url('admin/categorias/nueva/') ?>">
		<i class="large material-icons">add</i>
	</a>
</div>
<div id="<?php echo $modalid; ?>" class="modal" >
	<div class="modal-content">
		<h4><i class="material-icons">warning</i> Eliminar categoria</h4>
		<p>¿Desea eliminar ésta categoria?</p>
	</div>
	<div class="modal-footer">
		<a href="#!" data-action="acept" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
		<a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>
