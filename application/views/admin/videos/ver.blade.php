@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
	<div class="row">
		<div class="col s12 m7 l8" id="<?php echo $itemid; ?>">
			<div class="card event">
				<div class="card-action">
					<?php if (5 > $this->session->userdata('level')): ?>
					<div class="switch left">
						<label>
							No publicado
							<input type="checkbox" class="change_state" name="status"
								<?php if ($video['status']=="1"): ?> checked="checked"
								data-url="admin/videos/fn_ajax_change_state/"
								data-action-param='{"id":"<?php echo $video['id']; ?>", "table":"video"}'
								<?php endif ?>>
							<span class="lever"></span>
							Publicado
						</label>

					</div>
					<?php endif ?>
					<a href="#!" data-activates='options' class="dropdown-button right"><i
							class="material-icons">more_vert</i></a>
					<?php echo $options; ?>
				</div>
				<div class="card-image">
					<img src="<?php echo base_url($video['preview']); ?>" alt="" class="materialboxed">
				</div>
				<div class="card-content">
					<p><?php echo $video['description']; ?> <br></p>
				</div>
			</div>
		</div>
		<div class="col s12 m5 l4">
			<div class="card">
				<div class="card-content">
					<span class="card-title grey-text text-darken-4"><?php echo $video['nombre']; ?></span>
					<p><?php echo $video['fecha']; ?></p>
				</div>
				<div class="card-action">
					Duracion: <?php echo $video['duration']; ?> <br>
					Informacion de pago: <?php echo $video['payinfo']; ?> <br>
					URL Youtube: <?php echo $video['youtubeid']; ?> <br>
					<?php if ($categorias): ?>
					<b>Categorias:</b>
					<ul>
						<?php foreach ($categorias as $key => $value): ?>
						<li><a href="#"><?php echo $value['categoria']; ?></a></li>
						<?php endforeach ?>
					</ul>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<div id="<?php echo $modalid; ?>" class="modal">
	<div class="modal-content">
		<h4>Eliminar video</h4>
		<p>¿Desea eliminar éste video? Esta accion no podrá deshacerse</p>
	</div>
	<div class="modal-footer">
		<a href="#!" data-action="acept" class=" modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>
@endsection