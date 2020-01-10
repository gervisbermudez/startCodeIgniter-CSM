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
					<?php echo form_open_multipart('Admin/Galeria/savealbum/');?>
					<div class="row">
						<div class="input-field col s12">
							<input id="first_name2" type="text" name="nombre" class="validate">
							<label for="first_name2">Nombre del Album</label>
						</div>
					</div>
					<div class="form-group" >
						<label for="id_cazary" class="capitalize">Descripcion del Album:</label>
						<textarea class="form-control" id="id_cazary" placeholder="Descripcion del Album" name="descripcion" value=""></textarea>
					</div>
					<br><br>
					<label for="publishdate" class="capitalize">Fecha del album:</label>
					<div class="form-group input-group">
						<input type="date" class="datepicker" placeholder="Fecha" name="fecha" required="required">
					</div>
					<br><br>
					<div class="col s12">
									Publicar album
									<!-- Switch -->
									<div class="switch">
										<label>
											No publicado
											<input type="checkbox" name="estatus">
											<span class="lever"></span>
											Publicado
										</label>
						</div>
					</div>
					<div class="col s12">
						<br><br>
						<button type="submit" class="btn"><i class="material-icons right">done</i>Guardar</button>
						<a href="<?php echo base_url().'index.php/Admin/Galeria/' ?>" class="btn">Cancelar</a>
					</div>
					
					</form>						<!-- /.row (nested) -->
					
					<!-- /.row (nested) -->
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
</div>
</div>
<!-- /.container-fluid -->
<script type="text/javascript">
	jQuery(document).ready(function($) {
	$('select').material_select();
	});
</script>
