<div class="side-nav" id="slide-out" >
	<div class="user white-text">
		<div class="group">
			<a href="<?php echo base_url('admin/user/ver/'.$this->session->userdata('id')) ?>" title="Perfil">
			<?php if ($this->session->userdata('avatar')): ?>
			<img src="<?php echo base_url('public/img/profile/'.$this->session->userdata('username').'/'.$this->session->userdata('avatar')); ?>" alt="" class="circle z-depth-1">
			<?php else: ?>
			<i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
			<?php endif ?>
			</a>
			<a class="dropdown-button white-text" href="#!" data-activates="dropdown2"><?php echo $this->session->userdata('username'); ?><i class="mdi-navigation-arrow-drop-down right"></i></a>
		</div>
		<span class="level"><?php echo $this->session->userdata('type'); ?></span>
		<ul id='dropdown2' class='dropdown-content'>
			<li><a href="<?php echo base_url('admin/user/ver/'.$this->session->userdata('id')) ?>">Perfil</a></li>
			<li><a href="<?php echo base_url('admin/user/edit/'.$this->session->userdata('id')) ?>">Editar perfil</a></li>
			<li><a target="_blank" href="<?php echo base_url(); ?>">Ir al sitio</a></li>
			<li><a href="<?php echo base_url('Login/'); ?>"> Cerrar sesion</a>
		</ul>
	</div>
	<ul class="">
		<li><a href="<?php echo base_url('admin/user/') ?>"><i class="material-icons">perm_identity</i> Usuarios</a></li>
		<li><a href="<?php echo base_url('admin/mensajes/') ?>"><i class="material-icons">email</i> Mensajes</a></li>
		<li><a href="<?php echo base_url('admin/suscriptores/') ?>"><i class="material-icons">supervisor_account</i> Suscriptores</a></li>
		<li><a href="<?php echo base_url('admin/archivos') ?>"><i class="material-icons">markunread_mailbox</i> Archivos</a></li>
		<li><a href="<?php echo base_url('admin/categorias/') ?>"><i class="material-icons">receipt</i> Categorias</a></li>
		<li><a href="<?php echo base_url('admin/eventos/') ?>"><i class="material-icons">receipt</i> Eventos</a></li>
		<li><a href="<?php echo base_url('admin/galeria') ?>"><i class="material-icons">perm_media</i> Galería</a></li>
		<li><a href="<?php echo base_url('admin/videos') ?>"><i class="material-icons">video_library</i> Videos</a></li>
	</ul>
</div>
<nav>
	<div class="nav-wrapper">
		<a href="<?php echo base_url('admin/'); ?>" class="brand-logo">Admin</a>
		<a href="#" data-activates="slide-out" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
		<?php  
		/*<ul class="right hide-on-med-and-down">
			<li><a class="dropdown-button" href="#!" data-activates="dropnotify"><i class="material-icons right">notifications</i></a></li>
		</ul>
		<div id="dropnotify" class="dropdown-content">
			<div class="collection">
				<a href="#!" class="collection-item"><span class="secondary-content"><i class="material-icons">send</i></span>Miguel<span class="badge">1</span></a>
				<a href="#!" class="collection-item"><span class="secondary-content"><i class="material-icons teal-text text-lighten-1">email</i></span>Alan<span class="new badge">4</span></a>
				<a href="#!" class="collection-item">Juan</a>
				<a href="#!" class="collection-item">Ana <span class="badge new red">4</span></a>
			</div>
		</div>*/
		?>
	</div>
</nav>