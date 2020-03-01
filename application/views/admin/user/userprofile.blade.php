@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div>
	<div class="row">
		<div class="col s12">
			<div class="card banner">
				<!-- Dropdown Trigger -->
				<a class='dropdown-trigger right' href='#' data-target='<?= $dropdown_id ?>'>
				<i class="material-icons">more_vert</i></a>
				<?= $dropdown_menu ?>
				<div class="card-image">
					<img src="<?= base_url('public/img/profile/usertop.jpg'); ?>">
				</div>
				<div class="avatar">
					@if (isset($user->user_data->avatar))
					<a href="#modal2" class="modal-trigger">
						<img src="<?= base_url('public/img/profile/'.$user->username.'/'.$user->user_data->avatar); ?>" alt="<?= $user->username ?>" 
						class="circle z-depth-1">
					</a>
					@else
					<a href="#modal2" class="modal-trigger">
						<i class="material-icons circle grey lighten-5 z-depth-1">account_circle</i></a>						
					@endif
				</div>
				<div class="card-content row">
					<div class="col s12 m6">
						<span class="card-title"><?= $user->user_data->nombre ?>
							<?= $user->user_data->apellido ?></span>
							<p>
								<?= $user->role ?>
							</p>
					</div>
					<div class="col s12 m6">
						<span class="card-title"><?= $user->username ?></span>
						<p>
							Ultima vez: <?= $user->lastseen ?>
						</p>
					</div>


				</div>
			</div>
		</div>
		<div class="col s12 m5">
			<ul class="collection with-header">
				<li class="collection-header">
					<h4>Datos del usuario</h4>
				</li>
				<li class="collection-item"><i class="material-icons left">message</i>
					<a href="#!"><?= $user->email ?></a>
				</li>
				<li class="collection-item"><i class="material-icons left">contact_phone</i>
					<a href="#!"><?= $user->user_data->telefono ?></a>
				</li>
				<li class="collection-item"><i class="material-icons left">location_on</i>
					<a href="#!"><?= $user->user_data->direccion ?></a>
				</li>
			</ul>
		</div>
		<div class="col s12 m7 timeline"></div>
	</div>
</div>
<div id="modal2" class="modal bottom-sheet">
	<div class="modal-content">
		<h4>Cambiar foto de perfil</h4>
	</div>
	<div class="modal-footer">
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>
@endsection