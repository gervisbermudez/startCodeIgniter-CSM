<div class="main">
	<?php echo $header ?>
	<div class="container">
		<div class="row">
			<form action="<?php echo $base_url.'index.php/Admin/Eventos/save/';?>" method="post" id="form" class="col s12 m10 l10">
				<span class="header grey-text text-darken-2">Información <i class="material-icons left">description</i></span>
				<div class="input-field">
					<label for="nombre" >Nombre:</label>
					<input type="text" id="nombre" name="nombre" required="required">
				</div>
				<div class="input-field" >
					<label for="titulo" >Titulo:</label>
					<input type="text" id="titulo" name="titulo" required="required">
				</div>
				<div id="introduction" class="section scrollspy">
					
					<label for="id_cazary" >Descripcion del evento:</label>
					<div class="input-field">
						<textarea  id="id_cazary" name="texto"></textarea>
					</div>
					<br>
				</div>
				<span class="header grey-text text-darken-2">Ubicación <i class="material-icons left">room</i></span>
				<div class="input-field">
					<label for="ciudad" >Ciudad:</label>
					<input  id="ciudad" type="text" name="ciudad">
				</div>
				<div class="input-field">
					<label for="lugar" >Lugar:</label>
					<input type="text" name="lugar" id="lugar">
				</div>
				<div id="initialization" class="section scrollspy">
					<span class="header grey-text text-darken-2">Fecha <i class="material-icons left">schedule</i></span>
					<div class="input-field">
						<input type="date" placeholder="Fecha del evento" class="datepicker" id="publishdate" name="publishdate" required="required">
					</div>
					<div class="input-field">
						<label for="fecha" >Mostrar fecha:</label>
						<input type="text" name="fecha" id="fecha">
					</div>
					<br>
				</div>
				<span class="header grey-text text-darken-2">Imagen <i class="material-icons left">perm_media</i></span>
				<div class="input-field" >
					<input  type="text" id="imagen" class="modal-trigger" name="imagen" placeholder="Imagen principal" data-target="modal1"  data-expected="inactive" >
				</div>
				<div id="structure" class="section scrollspy">
					<div class="input-field">
						<input type="text" data-target="modal1" id="thumb" name="thumb" class="modal-trigger"  placeholder="Imagen Thumb"  data-expected="inactive" >
					</div>
					
				</div>
				<?php if (5 > $this->session->userdata('level')): ?>
				<div class="form-group">
					Publicar evento
					<!-- Switch -->
					<div class="switch">
						<label>
							No publicado
							<input type="checkbox" name="status" value="on">
							<span class="lever"></span>
							Publicado
						</label>
					</div>
				</div>
				<?php endif ?>
				<br>
				<div class="form-group" id="buttons">
					<a href="<?php echo base_url('admin/eventos/'); ?>" class="btn red darken-1">Cancelar</a>
					<button type="submit" class="btn btn-primary"><i class="material-icons right">done</i> Guardar</button>
					
				</div>
			</form>
			<!-- Modal Trigger -->
			
			<div class="col hide-on-small-only m2 l2">
				<ul class="section table-of-contents tabs-wrapper">
					<li><a href="#introduction">Información</a></li>
					<li><a href="#initialization">Ubicación</a></li>
					<li><a href="#structure">Imagen</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="modal1" class="modal bottom-sheet" data-navigatefiles="active">
	<div class="modal-content">
		<h4>Seleccionar imagen</h4>
		<div class="row gallery" data-active-dir="./img">
		</div>
	</div>
</div>