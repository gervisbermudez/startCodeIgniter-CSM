<div class="sidemenu">
    <a href="{{ base_url('admin/') }}" class="brand-logo">{{ADMIN_BRAND_NAME}}</a>
    <a href="#" class="sidenav-trigger-lg hide-on-med-and-down"><i class="material-icons">menu</i></a>
    <ul id="slide-out" class="sidenav collapsible">
        <li class="show-on-medium-and-down">
            <a class="waves-effect" href="{{ base_url('admin') }}"><i class="material-icons">dashboard</i>
                Dashboard</a>
        </li>
        <li class="{{isSectionActive('usuarios')}}">
            <div class="collapsible-header">
                <i class="material-icons">perm_identity</i> <span>Usuarios</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/usuarios/') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/usuarios/agregar/') }}">Nuevo</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('paginas')}}">
            <div class="collapsible-header">
                <i class="material-icons">web</i>
                <span>Paginas</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/paginas/') }}">Todas</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/paginas/nueva/') }}">Nueva</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('admin/formularios', 'match')}}">
            <div class="collapsible-header">
                <i class="material-icons">web</i>
                <span>Formularios</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/formularios/') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/formularios/new') }}">Nuevo</a>
                    </li>
                </ul>
            </div>
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
            <a class="waves-effect" href="{{ base_url('admin/suscriptores/') }}"><i class="material-icons">supervisor_account</i> Suscriptores</a>
        </li>
        <li class="{{isSectionActive('archivos')}}">
            <a class="waves-effect" href="{{ base_url('admin/archivos') }}"><i class="material-icons">markunread_mailbox</i>
                Archivos</a>
        </li>
        <li class="{{isSectionActive('categorias')}}">
            <div class="collapsible-header">
                <i class="material-icons">receipt</i>
                <span>Categorias</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a href="{{ base_url('admin/categorias/') }}">Todas</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/categorias/nueva/') }}">Nueva</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('eventos')}}">
            <div class="collapsible-header">
                <i class="material-icons">assistant</i>
                <span>Eventos</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/eventos/') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/eventos/agregar/') }}">Nuevo</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('galeria')}}">
            <div class="collapsible-header">
                <i class="material-icons">perm_media</i>
                <span>Albumes</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/galeria') }}"> Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/galeria/crearalbum/') }}">Nuevo Album</a>
                    </li>
                </ul>
            </div>

        </li>
        <li class="{{isSectionActive('videos')}}">
            <div class="collapsible-header">
                <i class="material-icons">video_library</i>
                <span>Videos</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/videos') }}"> Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/videos/nuevo/') }}">Crear Video</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>
