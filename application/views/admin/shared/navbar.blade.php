<nav>
	<!-- navbar content here  -->
	<div class="nav-wrapper">
		<a href="{{ base_url('admin/') }}" class="brand-logo">Start CMS</a>
		<a href="#" class="sidenav-trigger-lg hide-on-med-and-down"><i class="material-icons">menu</i></a>
		<a href="#" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>
		<!-- Dropdown Trigger -->
		<a class='dropdown-trigger right' href='#' data-target='dropdown1'>
			@if ($ci->session->userdata('avatar'))
			<img src="{{base_url(IMGPROFILEPATH) . $ci->session->userdata('username') . '/' . $ci->session->userdata('avatar') }}"
				alt="" class="circle z-depth-1" />
			@else
			<i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
			@endif
		</a>
		<!-- Dropdown Structure -->
		<div id="dropdown1" class="dropdown-content user-dropdown">
			<div class="user-view">
				<div class="background">
				</div>
				<a href="{{base_url('admin/user/ver/' . $ci->session->userdata('id')) }}" class="user-avatar">
					@if ($ci->session->userdata('avatar'))
					<img src="{{base_url(IMGPROFILEPATH) . $ci->session->userdata('username') . '/' . $ci->session->userdata('avatar') }}"
						alt="" class="circle z-depth-1" />
					@else
					<i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
					@endif
				</a>
				<a class="avatar-username" href="{{base_url('admin/user/ver/' . $ci->session->userdata('id')) }}">
					<span class="white-text name">{{$ci->session->userdata('username') }}</span>
				</a>
				<a class="avatar-email" href="#email">
					<span class="white-text email">{{$ci->session->userdata('type') }}</span>
				</a>
			</div>
			<ul class="menu">
				<li class="divider" tabindex="-1"></li>
				<li><a target="_blank" href="{{ base_url() }}"><i class="material-icons">launch</i> Ir al sitio</a></li>
				<li><a href="{{ base_url('admin/login/') }}"> Cerrar sesion</a></li>
			</ul>
		</div>
	</div>
</nav>
<ul id="slide-out" class="sidenav">
	<li class="{{isSectionActive('usuarios')}}">
		<a class="waves-effect" href="{{ base_url('admin/usuarios/') }}"><i class="material-icons">perm_identity</i>
			Usuarios</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/usuarios/agregar/') }}">Nuevo</a>
			</li>
		</ul>
	</li>
	<li class="{{isSectionActive('paginas')}}">
		<a class="waves-effect" href="{{ base_url('admin/paginas/') }}"><i class="material-icons">web</i>
			Paginas</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/paginas/nueva/') }}">Nueva</a>
			</li>
		</ul>
	</li>
	<li class="{{isSectionActive('formularios')}}">
		<a class="waves-effect" href="{{ base_url('admin/formularios/') }}"><i class="material-icons">web</i>
			Api Forms</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/formularios/new') }}">Nuevo</a>
			</li>
		</ul>
	</li>
	<li class="{{isSectionActive('content')}}">
		<a class="waves-effect" href="{{ base_url('admin/content') }}"><i class="material-icons">insert_chart</i>
			Forms Content</a>
	</li>
	<li class="{{isSectionActive('mensajes')}}">
		<a class="waves-effect" href="{{ base_url('admin/mensajes/') }}"><i class="material-icons">email</i>
			Mensajes</a>
	</li>
	<li class="{{isSectionActive('suscriptores')}}">
		<a class="waves-effect" href="{{ base_url('admin/suscriptores/') }}"><i
				class="material-icons">supervisor_account</i> Suscriptores</a>
	</li>
	<li class="{{isSectionActive('archivos')}}">
		<a class="waves-effect" href="{{ base_url('admin/archivos') }}"><i class="material-icons">markunread_mailbox</i>
			Archivos</a>
	</li>
	<li class="{{isSectionActive('categorias')}}">
		<a class="waves-effect" href="{{ base_url('admin/categorias/') }}"><i class="material-icons">receipt</i>
			Categorias</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/categorias/nueva/') }}">Nueva</a>
			</li>
		</ul>
	</li>
	<li class="{{isSectionActive('eventos')}}">
		<a class="waves-effect" href="{{ base_url('admin/eventos/') }}"><i class="material-icons">assistant</i>
			Eventos</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/eventos/agregar/') }}">Nuevo</a>
			</li>
		</ul>
	</li>
	<li class="{{isSectionActive('galeria')}}">
		<a class="waves-effect" href="{{ base_url('admin/galeria') }}"><i class="material-icons">perm_media</i>
			Fotos</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/galeria/crearalbum/') }}">Nuevo Album</a>
			</li>
		</ul>
	</li>
	<li class="{{isSectionActive('videos')}}">
		<a class="waves-effect" href="{{ base_url('admin/videos') }}"><i class="material-icons">video_library</i>
			Videos</a>
		<ul>
			<li>
				<a href="{{ base_url('admin/videos/nuevo/') }}">Crear Video</a>
			</li>
		</ul>
	</li>
</ul>