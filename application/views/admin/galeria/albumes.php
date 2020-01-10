<div class="main">
	<?php echo $header; ?>
	<div class="container">
		<div class="row">

			<?php
				$this->load->helper('array');
				$quotes = array(" indigo", "blue"," cyan",  "green", "blue-grey", "pink", "lime", 'grey', 'orange');
				$deep = array("darken-1", 'accent-4', '');
				$modalid = random_string('alnum', 16);
			?>
			<div class="col s12">
				<div class="row">
					<?php if ($albumes): ?>
					<?php foreach ($albumes as $key => $value): ?>
					<?php 
						$itemid = random_string('alnum', 16);
						$ddmid = random_string('alnum', 16);
					?>
					<div class="col m4 s6" id="<?php echo $itemid ?>">
						<div class="card album hoverable">
							<a class='dropdown-button right white-text' href='#' data-activates='<?php echo $ddmid ?>'>
							<i class="material-icons">more_vert</i></a>
						<!-- Dropdown Structure -->
						<ul id='<?php echo "$ddmid" ?>' class='dropdown-content'>
							<?php if (6 > $this->session->userdata('level')): ?>
							<li>
								<a href="#<?php echo $modalid;?>" class="modal-trigger" data-action-param='{"id":"<?php echo $value['id'] ?>", "table":"album"}' data-url="admin/galeria/fn_ajax_delete_data/" data-ajax-action="inactive" data-target-selector="<?php echo "#$itemid"; ?>">Eliminar</a>
							</li>
							<?php endif ?>
							<li><a href="<?php echo base_url('admin/galeria/editaralbum/'.$value['id']); ?>"> Editar</a></li>
						</ul>
						<a class="card-content <?php echo random_element($quotes); ?> <?php echo random_element($deep); ?>" href="<?php echo base_url('admin/galeria/albumes/'.$value['id']); ?>">
							<div class="center-align white-text"><i class="material-icons medium ">perm_media</i></div>
							<div class="center-align white-text"><?php echo $value['nombre'] ?></div>
							<!-- Dropdown Trigger -->
						</a>
						<div class="card-action">
							<a href="<?php echo base_url('admin/galeria/albumes/'.$value['id']); ?>">
								<span class="pull-left">Ver Álbum</span>
							</a>
							<?php if ($value['status'] === '1'): ?>
							<i class="material-icons tooltipped right" data-position="left" data-delay="50" data-tooltip="Publico">assignment_ind</i>
							<?php else: ?>
							<i class="material-icons tooltipped right" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
							<?php endif ?>
						</div>
						
					</div>
				</div>
				<?php endforeach ?>
				<?php else: ?>
					No hay albumes
				<?php endif ?>
				
			</div>
			<!-- Modal Trigger -->
			<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
				<a class="btn-floating btn-large red waves-effect waves-circle waves-light new tooltipped " data-position="left" data-delay="50" data-tooltip="Crear Álbum" href="<?php echo base_url('admin/galeria/crearalbum/'); ?>"><i class="material-icons">add</i>
				</a>
			</div>
		</div>
	</div>
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