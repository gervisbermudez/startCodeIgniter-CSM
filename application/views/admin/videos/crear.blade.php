@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
	<div class="row">
		<form action="<?php echo base_url($action);?>" method="post" id="form" class="col s12 m10 l10">
			<input type="hidden" name="id" value="<?php echo element('id', $video, ''); ?>">
			<span class="header grey-text text-darken-2">Datos básicos <i
					class="material-icons left">description</i></span>
			<div class="input-field">
				<label for="nombre">Nombre:</label>
				<input type="text" id="nombre" name="nombre" required="required"
					value="<?php echo element('nombre', $video, ''); ?>">
			</div>
			<div id="introduction" class="section scrollspy">
				<label for="id_cazary">Descripcion del video:</label>
				<div class="input-field">
					<textarea id="id_cazary" name="description">
							<?php echo element('description', $video, ''); ?>
						</textarea>
				</div>
				<br>
			</div>
			<div class="input-field">
				<label for="duracion">Duracion:</label>
				<input type="text" id="duracion" name="duration" required="required"
					value="<?php echo element('duration', $video, ''); ?>">
			</div>
			<div class="input-field">
				<label for="youtubeid">URL Youtube:</label>
				<input type="text" id="youtubeid" name="youtubeid" required="required"
					value="<?php echo element('youtubeid', $video, ''); ?>">
			</div>
			<div class="input-field">
				<label for="paypal">Informacion de pago:</label>
				<input type="text" id="paypal" name="paypal" required="required"
					value="<?php echo element('payinfo', $video, '');?>">
			</div>
			<span id="preview" class="header grey-text text-darken-2 scrollspy">Preview <i
					class="material-icons left">perm_media</i></span>
			<div class="input-field">
				<input type="text" id="imagen" class="modal-trigger" name="preview" placeholder="Imagen principal"
					data-expected="inactive" data-target="modal1" value="<?php echo element('preview', $video, '');?>">
			</div>
			<br>
			<div class="input-field">
				<select name="categorias[]" multiple>
					<option value="" disabled selected>Selecciona</option>
					<?php if ($categorias): ?>
					<?php foreach ($categorias as $key => $categoria): ?>
					<option value="<?php echo $categoria['id'] ?>"
						<?php if (array_key_exists($categoria['id'], $videocategoria)): ?> selected <?php endif ?>>
						<?php echo $categoria['nombre'] ?></option>
					<?php endforeach ?>
					<?php endif ?>
				</select>
				<label>Categorias:</label>
			</div>
			<?php if (5 > $ci->session->userdata('level')): ?>
			<!-- Switch -->
			<br>
			Publicar video
			<br>
			<div class="input-field">
				<div class="switch">
					<label>
						No publicado
						<input type="checkbox" name="status" value="1" <?php if (element('status', $video, '')=="1"): ?>
							checked="checked" <?php endif ?>>
						<span class="lever"></span>
						Publicado
					</label>
				</div>
			</div>
			<?php endif ?>
			<br><br>
			<div class="input-field" id="buttons">
				<a href="<?php echo base_url('admin/videos/'); ?>" class="btn red darken-1">Cancelar</a>
				<button type="submit" class="btn btn-primary"><i class="material-icons right">done</i> Guardar</button>

			</div>
		</form>
		<div class="col hide-on-small-only m2 l2">
			<ul class="section table-of-contents tabs-wrapper">
				<li><a href="#introduction">Información</a></li>
				<li><a href="#preview">Imagen</a></li>
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
@endsection