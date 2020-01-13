<nav>
	<!-- navbar content here  -->
	<div class="nav-wrapper">
		<a href="{{ base_url('admin/') }}" class="brand-logo">Admin</a>
		<a href="#" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>
	</div>
</nav>
<ul id="slide-out" class="sidenav">
	<li>
		<div class="user-view">
			<div class="background">
				<img src="{{ base_url(IMGPATH) }}admin/userwallpaper-7.jpg" />
			</div>
			<a href="#user">
				@if ($ci->session->userdata('avatar'))
				<img src="{{base_url(IMGPROFILEPATH) . $ci->session->userdata('username') . '/' . $ci->session->userdata('avatar') }}"
					alt="" class="circle z-depth-1" />
				@else
				<i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
				@endif
			</a>
			<a href="{{base_url('admin/user/ver/' . $ci->session->userdata('id')) }}"><span
					class="white-text name">{{$ci->session->userdata('username') }}</span></a>
			<a href="#email"><span class="white-text email"></span></a>
		</div>
	</li>
	<li>
		<a target="_blank" href="{{ base_url() }}"><i class="material-icons">cloud</i> Ir al sitio</a>
	</li>
	<li><a href="{{ base_url('Login/') }}"> Cerrar sesion</a></li>
	<li>
		<div class="divider"></div>
	</li>
	<li><a class="subheader">Modules</a></li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/user/') }}"><i class="material-icons">perm_identity</i>
			Usuarios</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/mensajes/') }}"><i class="material-icons">email</i>
			Mensajes</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/suscriptores/') }}"><i
				class="material-icons">supervisor_account</i> Suscriptores</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/archivos') }}"><i class="material-icons">markunread_mailbox</i>
			Archivos</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/categorias/') }}"><i class="material-icons">receipt</i>
			Categorias</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/eventos/') }}"><i class="material-icons">receipt</i>
			Eventos</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/galeria') }}"><i class="material-icons">perm_media</i>
			Galería</a>
	</li>
	<li>
		<a class="waves-effect" href="{{ base_url('admin/videos') }}"><i class="material-icons">video_library</i>
			Videos</a>
	</li>
</ul>