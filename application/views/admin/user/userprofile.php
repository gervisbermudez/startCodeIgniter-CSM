<?php
	function get($data = array(), $key)
	{
		if ($data) {
			if (array_key_exists("$key",$data)) {
				return $data["$key"];
			}
		}
		return false;
	}
?>
<?php
		$this->load->helper('array');
?>
<div class="main">
	<div class="container">
		<div class="row">
			<div class="col s12">
				<div class="card banner">
					<div class="card-image">
						<a class='dropdown-button right' href='#' data-activates='dropdown'><i class="material-icons">more_vert</i></a>
						<?php echo $options; ?>
						<img src="<?php echo base_url('public/img/profile/usertop.jpg'); ?>">
					</div>
					<div class="avatar">
						<?php if (array_key_exists('avatar',$userdata[0])): ?>
						<a href="#modal2" class="modal-trigger"><img src="<?php echo base_url('public/img/profile/'.$userdata[0]['username'].'/'.$userdata[0]['avatar']); ?>" alt="<?php echo $userdata[0]['avatar'] ?>" class="circle z-depth-1"></a>
						<?php else: ?>
						<a href="#modal2" class="modal-trigger"><i class="material-icons circle grey lighten-5 z-depth-1">account_circle</i></a>
						<?php endif ?>
					</div>
					
					<div class="card-content row">
						<div class="col s12 m6">
							<span class="card-title"><?php echo $userdata[0]['nombre'] ?> <?php echo $userdata[0]['apellido'] ?></span><br>
							<span ><?php echo $userdata[0]['name'] ?></span>
						</div>
						<div class="col s12 m6">
							<span class="card-title"><?php echo $userdata[0]['username'] ?></span>
							<p>
								Ultima vez: <?php echo $userdata[0]['lastseen'] ?>
							</p>
						</div>
						
						
					</div>
				</div>
			</div>
			<div class="col s12 m5">
				<ul class="collection with-header">
					<li class="collection-header"><h4>Datos del usuario</h4></li>
					<li class="collection-item"><i class="material-icons left">message</i> <?php echo $userdata[0]['email'] ?></li>
					<li class="collection-item"><i class="material-icons left">contact_phone</i> <?php echo $userdata[0]['telefono'] ?></li>
					<li class="collection-item"><i class="material-icons left">location_on</i> <?php echo $userdata[0]['direccion'] ?></li>
					<?php if (array_key_exists('create by',$userdata[0])): ?>
					<li class="collection-item"><i class="material-icons left">perm_contact_calendar</i> Create by <?php echo $userdata[0]['create by'] ?></li>
					<?php endif ?>
				</ul>
			</div>
			<div class="col s12 m7 timeline">
				<?php
					$this->load->helper('array');
					$quotes = array(" indigo", "blue"," cyan",  "green", "blue-grey", "pink", "lime", 'grey', 'orange');
					$deep = array("darken-1", 'accent-4', '');
				?>
				<?php if ($relations): ?>
				<?php foreach ($relations as $key => $value): ?>
				<?php if ($value['tiperel'] === 'album'): ?>
				<div class="row">
					<div class="col s12">
						<div class="card album hoverable">
							<a class="card-content <?php echo random_element($quotes); ?> <?php echo random_element($deep); ?>" href="<?php echo base_url('admin/galeria/albumes/'.$value['id']); ?>">
								<span class="card-title white-text"><?php echo $value['nombre'] ?></span>
								<br>
								<span class="white-text"><?php echo $value['fecha'] ?></span>
								
							</a>
							<div class="card-action">
								<a href="<?php echo base_url('admin/galeria/albumes/'.$value['id']); ?>/">Ver album</a>
								<span class="estatus right">
									<?php if ($value['status'] === 'status'): ?>
									<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">assignment_ind</i>
									<?php else: ?>
									<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
									<?php endif ?>
								</span>
							</div>
						</div>
					</div>
				</div>
				<?php elseif($value['tiperel'] === 'video'): ?>
					<div class="row">
					<div class="col s12">
						<div class="card album hoverable">
							<div class="card-image waves-effect waves-block waves-light">
								<img src="<?php echo base_url($value['preview']);?>" class="activator" >
								<span class="card-title"><?php echo $value['nombre'] ?></span>
							</div>
							<div class="card-action">
								<a href="<?php echo base_url('admin/videos/ver/'.$value['id']); ?>">Ver Video</a>
								<span class="estatus right">
									<?php if ($value['status'] === '1'): ?>
									<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">assignment_ind</i>
									<?php else: ?>
									<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
									<?php endif ?>
								</span>
							</div>
							<div class="card-reveal">
								<span class="card-title grey-text text-darken-4"><?php echo $value['nombre'] ?><i class="material-icons right">close</i></span>
								<p>
									duration: <?php echo $value['duration'] ?><br>
									youtubeid: <?php echo $value['youtubeid'] ?><br>
									<?php echo $value['fecha'] ?>
								</p>
							</div>
						</div>
					</div>
				</div>
				<?php else: ?>
				<div class="row">
					<div class="col s12">
						<div class="card album hoverable">
							<div class="card-image waves-effect waves-block waves-light">
								<img src="<?php echo base_url('public/img/portfolio/'.$value['imagen']);?>" class="activator" >
								<span class="card-title"><?php echo $value['titulo'] ?></span>
							</div>
							<div class="card-action">
								<a href="<?php echo base_url('admin/eventos/ver/'.$value['id']); ?>">Ver Evento</a>
								<span class="estatus right">
									<?php if ($value['status'] === '1'): ?>
									<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">assignment_ind</i>
									<?php else: ?>
									<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
									<?php endif ?>
								</span>
							</div>
							<div class="card-reveal">
								<span class="card-title grey-text text-darken-4"><?php echo $value['nombre'] ?><i class="material-icons right">close</i></span>
								<p>
									<?php echo $value['ciudad'] ?><br>
									<?php echo $value['lugar'] ?><br>
									<?php echo $value['fecha'] ?>
								</p>
							</div>
						</div>
					</div>
				</div>
				<?php endif ?>
				<?php endforeach ?>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>
<div id="modal2" class="modal bottom-sheet">
	<div class="modal-content">
		<h4>Cambiar foto de perfil</h4>
		<?php echo $form ?>
	</div>
	<div class="modal-footer">
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>