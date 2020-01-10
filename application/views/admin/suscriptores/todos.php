<div class="main">
	<?php echo $header; ?>
	<div class="container">
		<div class="row">
			<div class="col s12">
				<div class="panel panel-default">
					<div class="panel-heading">
						Todos los Suscriptores:
					</div>
					<div class="panel-body">
						<?php if ($Suscriptores): ?>
						
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Nombre</th>
										<th>Email</th>
										<th>Recibir</th>
										<th>Fecha</th>
										<th>Estatus</th>
										<?php if ($this->session->userdata('type') == "Administrador"): ?>
										<td><i class="fa fa-trash"></i></td>
										<?php endif ?>
									</tr>
								</thead>
								<tbody>
									<?php $i = 0; foreach ($Suscriptores as $Suscriptor): ?>
									<tr>
										<td><?php $i++; echo $i; ?></td>
										<td><?php echo $Suscriptor['nombre'] ?></td>
										<td><a href="mailto:<?php echo $Suscriptor['email'] ?>"><i class="fa fa-envelope-o"></i> <?php echo $array['email'] ?></a></td>
										<td><?php echo $Suscriptor['recibir'] ?></td>
										<td><?php echo $Suscriptor['fecha'] ?></td>
										<td><?php echo $Suscriptor['estatus'] ?></td>
										<?php if ($this->session->userdata('type') == "Administrador"): ?>
										<td><a href="<?php echo $base_url.'index.php/Admin/Suscriptores/Eliminar/'.$array['id'] ?>" title="Eliminar"><i class="fa fa-trash"></i></a></td>
										<?php endif ?>
									</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
						<?php else: ?>
						No hay suscriptores
						<?php endif ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>