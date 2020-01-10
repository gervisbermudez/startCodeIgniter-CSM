<div class="main">
	<?php echo $header; ?>
	<div class="container">
		<div class="row">
			<div class="col s12">
				<div class="panel-heading">
					Usuarios registrados:
				</div>
				<?php if ($userdata): ?>
					
				
				<div class="table-responsive">
					<?php foreach ($userdata as $array): ?>
					<ul class="collection">
						<li class="collection-item avatar">
							<i class="material-icons circle cyan medium">
							<?php if ($array['usergroup']=='Administrador'): ?>
								supervisor_account
							<?php else: ?>
								perm_identity
							<?php endif ?></i>
							<span class="title"><b><?php echo $array['username'] ?></b></span>
							<p><?php echo $array['name'] ?><br>
								Ultima vez: <?php echo $array['lastseen'] ?>
							</p>
							<a href="<?php echo $base_url ?>index.php/admin/user/ver/<?php echo $array['id'] ?>" class="secondary-content tooltipped" data-position="left" data-delay="50" data-tooltip="Mas detalles" ><i class="material-icons cyan-text small">info_outline</i></a>
						</li>
					</ul>
					<?php endforeach ?>
				</div>
				<?php else: ?>
				No hay más usuarios registrados
				<?php endif ?>
			</div>
		</div>
	</div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
					<a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear Usuario"  href="<?php echo base_url('admin/user/agregar/') ?> ">
						<i class="large material-icons">add</i>
					</a>
				</div>