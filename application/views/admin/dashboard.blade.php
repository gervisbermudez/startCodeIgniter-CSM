@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container large">
	<div class="row">
		<?php if ($mensajes): ?>
		<div class="col s12 m6 l5">
			<div class="card sticky-action">
				<div class="card-content color3">
					<span class="card-title white-text"><span id="mensajesnumber"><?php echo $count_mensajes ?></span>
						mensajes sin leer</span>
					<a id="cardmensajeoptions" class="dropdown-button right white-text" href="#"
						data-activates="dropdown"><i class="material-icons">more_vert</i></a>

					<?php if ($this->session->userdata('type') == "Administrador"): ?>
					<ul class="dropdown-content" role="menu" id="dropdown">
						<li><a id="deletemensaje">Eliminar</a></li>
					</ul>
					<?php endif ?>

					<a href="#!" id="next"
						class="btn btn-floating btn-large color3 waves-effect waves-circle waves-light"><i
							class="material-icons">skip_next</i></a>
				</div>
				<div id="contenmensajes" class="card-content">
					<?php foreach ($mensajes as $key => $mensaje): ?>
					<div class="mensaje" id="mensajeid<?php echo $mensaje['id'] ?>">
						<div><b><?php echo $mensaje['nombre'] ?></b></div>
						<div><?php echo character_limiter($mensaje['mensaje'], 350) ?></div>
						<a class="mail" href="mailto:<?php echo $mensaje['email'] ?>"></a>
						<a class="url" href="<?php echo base_url('Admin/Mensajes/ver/'.$mensaje['id']) ?>"
							class="link"></a>
						<a href="#" data-id="<?php echo $mensaje['id'] ?>" class="deletelink"></a>
					</div>
					<?php endforeach ?>
				</div>
				<div class="card-action">
					<a href="#" id="replito">Responder</a>
					<a href="#" id="details">Ver detalles</a>
				</div>

			</div>
		</div>
		<?php else: ?>
		<div id="contentmensajes" class="col s12 m6 l5">
			<div class="card sticky-action">
				<div class="card-content color3">
					<span class="card-title white-text">Mensajes</span>
					<a href="#" class="btn btn-floating btn-large white-text"><i class="material-icons">done</i></a>
				</div>
				<div id="contenmensajes" class="card-content">
					No tienes mensajes nuevos
				</div>
			</div>
		</div>
		<?php endif ?>
		<div class="col s12 m6 l7">
			<div class="card sticky-action">
				<div class="card-content color3">
					<span class="card-title white-text">Eventos Recientes</span>
					<a href="<?php echo base_url().'index.php/Admin/Eventos/Agregar/' ?>"
						class="btn btn-floating btn-large color3 new"><i
							class="material-icons">add</i></a>
				</div>
				<div class="card-content">
					<div class="mainevents">
						<div class="collection">
							<?php if ($eventos): ?>
							<?php foreach ($eventos as $key => $evento): ?>
							<a class="collection-item avatar"
								href="<?php echo base_url().'index.php/Admin/Eventos/Ver/'.$evento['id'] ?>">
								<img src="<?php echo base_url().'img/portfolio/'.$evento['thumb'] ?>" alt=""
									class="circle">
								<span class="title"><?php echo $evento['nombre'] ?></span>
								<p><?php echo $evento['titulo'] ?><br>
									<?php echo $evento['fecha'] ?>
								</p>
							</a>
							<?php endforeach ?>
							<?php else: ?>
							No hay eventos para mostar
							<?php endif ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row main-page">
		<div class="col m12 s6 s12 l4">
			<div class="card card-main-page hoverable">
				<a class="card-content color3"
					href="<?php echo base_url(); ?>index.php/admin/galeria/crearalbum">
					<div class="center-align"><i class="material-icons medium white-text">queue</i></div>

					<div class="center-align white-text ">Agregar álbum nuevo</div>
					<!-- Dropdown Trigger -->
				</a>
				<div class="card-action">
					<a class="pull-right" href="<?php echo base_url(); ?>index.php/admin/galeria/crearalbum">
						<span>Nuevo</span>
					</a>

				</div>
			</div>
		</div>
		<?php if ($albumes): ?>
		<?php foreach ($albumes as $key => $album): ?>
		<div class="col m6 s6 l4">
			<div class="card card-main-page hoverable">
				<a class="card-content color3"
					href="<?php echo base_url('admin/galeria/albumes/').$album['id'] ?>">
					<div class="center-align"><i class="material-icons medium white-text">perm_media</i></div>

					<div class="center-align white-text "><?php echo $album['nombre'] ?></div>
					<!-- Dropdown Trigger -->
				</a>
				<div class="card-action">
					<a class="pull-right" href="<?php echo base_url('admin/galeria/albumes/').$album['id'] ?>">
						<span>Ver álbum</span>
					</a>

				</div>
			</div>
		</div>
		<?php endforeach ?>
		<?php endif ?>
	</div>
</div>
@endsection