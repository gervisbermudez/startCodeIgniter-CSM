<div class="sidemenu">
	<a href="{{ base_url('admin/') }}" class="brand-logo">{{ADMIN_BRAND_NAME}}</a>
	<a href="#" class="sidenav-trigger-lg hide-on-med-and-down"><i class="material-icons">menu</i></a>
	<ul id="slide-out" class="sidenav">
		<li class="show-on-medium-and-down">
			<a class="waves-effect" href="{{ base_url('admin') }}"><i class="material-icons">dashboard</i>
				Dashboard</a>
		</li>
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
		<li class="{{isSectionActive('admin/formularios', 'match')}}">
			<a class="waves-effect" href="{{ base_url('admin/formularios/') }}"><i class="material-icons">web</i>
				Formularios</a>
			<ul>
				<li>
					<a href="{{ base_url('admin/formularios/new') }}">Nuevo</a>
				</li>
			</ul>
		</li>
		<li class="{{isSectionActive('admin/formularios/content', 'match')}}">
			<a class="waves-effect" href="{{ base_url('admin/formularios/content') }}"><i class="material-icons">insert_chart</i>
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
			<a class="waves-effect" href="{{ base_url('admin/archivos') }}"><i
					class="material-icons">markunread_mailbox</i>
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
</div>
