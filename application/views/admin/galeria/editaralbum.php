<div class="main">
	<?php echo $header ?>
	<div class="container">
		<div class="row">
			<div class="col s12">
				<div class="panel panel-default">
					<div class="panel-heading">
						Crear Album
					</div>
					<div class="panel-body">
						<?php echo form_open_multipart('Admin/Galeria/savealbumedit/'.$album[0]['id']);?>
						
						<div class="form-group" id="g-Nombre">
							<label for="Nombre" class="capitalize">Nombre:</label>
							<input class="form-control" id="Nombre" placeholder="Nombre" name="nombre" value="<?php echo $album[0]['nombre'] ?>" required="required" autofocus>
							
						</div>
						<div class="form-group" >
							<label for="id_cazary" class="capitalize">Descripcion del Album:</label>
							<textarea class="form-control" id="id_cazary" placeholder="Descripcion del Album" name="descripcion" value="">
							<?php echo  $album[0]['descripcion'] ?>
							</textarea>
						</div>
						<br><br>
						<label for="publishdate" class="capitalize">Fecha del evento:</label>
						<div class="form-group input-group">
							<?php
								$fecha = DateTime::createFromFormat('Y-m-d H:i:s',$album[0]['fecha']);
							?>
							<input type="date" class="datepicker" placeholder="<?php echo $fecha->format('j F, Y') ?>" name="fecha" required="required" value="<?php echo $fecha->format('j F, Y') ?>">
						</div>						
						<div class="col s12">
							Publicar album
							<!-- Switch -->
							<div class="switch">
								<label>
									No publicado
									<input type="checkbox" name="estatus" <?php if ( $album[0]['estatus'] == 'Publicado'): ?> checked="checked" <?php endif ?> >
									<span class="lever"></span>
									Publicado
								</label>
							</div>
							<br>
						</div>
						
						<div class="form-group" id="buttons">
							<button type="submit" class="btn btn-primary"><i class="material-icons right">done</i>Guardar</button>
							<a href="<?php echo base_url('admin/galeria/albumes/'); ?>" class="btn btn-default">Cancelar</a>
						</div>
						
					</form>
				</div>
				
			</div>
			
		</div>
	</div>
</div>
</div>