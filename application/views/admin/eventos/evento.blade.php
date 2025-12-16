@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
	<div class="row">
		<div class="col s12"><?php if ($evento): ?>
			<div class="card event">
				<div class="card-action">
					<?php if (5 > $this->session->userdata('level')): ?>
					<div class="switch left">
						<label>
							No publicado
							<input type="checkbox" class="change_state" name="status"
								data-url="admin/events/fn_ajax_change_state/"
								data-action-param='{"id":"<?php echo $evento['id']; ?>", "table":"eventos"}'
								<?php if ($evento['status']=="1"): ?>checked="checked" <?php endif ?>>
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
					<img src="<?php echo base_url($evento['imagen']); ?>" alt="" class="materialboxed">
				</div>
				<div class="card-content">
					<span class="card-title"><?php echo $evento['nombre']; ?> <?php echo $evento['titulo'] ?></span>

					<p>
						<?php echo $evento['texto']; ?>
					</p>
				</div>
				<div class="card-action">
					<?php echo $evento['ciudad']; ?> <?php echo $evento['fecha']; ?> <?php echo $evento['lugar']; ?>
				</div>
			</div>
		</div>
		<?php else: ?>
		El evento al que intenta acceder no existe :(
		<?php endif ?>
	</div>
</div>
</div>
</div>
<div id="<?php echo $modalid; ?>" class="modal">
	<div class="modal-content">
		<h4>Eliminar Evento</h4>
		<p>¿Desea eliminar éste evento? Esta accion no podrá deshacerse</p>
	</div>
	<div class="modal-footer">
		<a href="#!" data-action="acept" class=" modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>
@endsection