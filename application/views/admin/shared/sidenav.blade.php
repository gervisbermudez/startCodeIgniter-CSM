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
                <i class="material-icons">people</i> <span>Usuarios</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_USERS'))
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/usuarios/') }}">Todos</a>
                    </li>
                    @endif
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/usuarios/usergroups') }}">Grupos</a>
                    </li>
                    @if(has_permisions('CREATE_USER'))
                    <li>
                        <a href="{{ base_url('admin/usuarios/agregar/') }}">Nuevo</a>
                    </li>
                    @endif
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
                    @if(has_permisions('SELECT_PAGES'))
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/paginas/') }}">Todas</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_PAGE'))
                    <li>
                        <a href="{{ base_url('admin/paginas/nueva/') }}">Nueva</a>
                    </li>
                    @endif

                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('siteforms')}}">
            <div class="collapsible-header">
                <i class="material-icons">assistant</i>
                <span>Formularios</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/siteforms') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/siteforms/nuevo/') }}">Nuevo</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/siteforms/submit/') }}">Recibidos</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('calendario')}}">
            <a class="waves-effect" href="{{ base_url('admin/calendario') }}"><i class="material-icons">event_note</i>
                Calendario</a>
        </li>
        <li class="{{isSectionActive('Fragments')}}">
            <a class="waves-effect" href="{{ base_url('admin/Fragments/') }}"><i
                    class="material-icons">bookmark_border</i>
                Fragmentos</a>
        </li>
        <li class="{{isSectionActive('suscriptores')}}">
            <a class="waves-effect" href="{{ base_url('admin/suscriptores/') }}"><i
                    class="material-icons">supervisor_account</i> Suscriptores</a>
        </li>
        @if(has_permisions('SELECT_FILES'))
        <li class="{{isSectionActive('archivos')}}">
            <a class="waves-effect" href="{{ base_url('admin/archivos') }}"><i
                    class="material-icons">markunread_mailbox</i>
                Archivos</a>
        </li>
        @endif
        @if(has_permisions('SELECT_MENUS'))
        <li class="{{isSectionActive('menu')}}">
            <div class="collapsible-header">
                <i class="material-icons">menu</i>
                <span>Menús </span>
            </div>
            <div class="collapsible-body">
                <ul>

                    <li>
                        <a href="{{ base_url('admin/menus/') }}">Todos</a>
                    </li>
                    @if(has_permisions('CREATE_MENU'))
                    <li>
                        <a href="{{ base_url('admin/menus/nuevo/') }}">Nuevo</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        <li class="{{isSectionActive('categorias')}}">
            <div class="collapsible-header">
                <i class="material-icons">receipt</i>
                <span>Categorias</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_CATEGORIES'))
                    <li>
                        <a href="{{ base_url('admin/categorias/') }}">Todas</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_CATEGORIE'))
                    <li>
                        <a href="{{ base_url('admin/categorias/nueva/') }}">Nueva</a>
                    </li>
                    @endif
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
                        <a href="{{ base_url('admin/galeria/nuevo/') }}">Nuevo Album</a>
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

        <li class="{{isSectionActive('admin/custommodels', 'match')}}">
            <div class="collapsible-header">
                <i class="material-icons">assessment</i>
                <span>Modelos</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_FORM_CUSTOMS'))
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/custommodels/') }}">Todos</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_FORM_CUSTOM'))
                    <li>
                        <a href="{{ base_url('admin/custommodels/nuevo') }}">Nuevo</a>
                    </li>
                    @endif
                    @if(has_permisions('SELECT_CONTENT_DATA'))
                    <li class="{{isSectionActive('admin/custommodels', 'match')}}">
                        <a class="waves-effect" href="{{ base_url('admin/custommodels/content') }}">
                            Contenidos</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('configuracion')}}">
            <div class="collapsible-header">
                <i class="material-icons">settings</i>
                <span>Configuración</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuracion') }}"> Configuración</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuracion/apilogger') }}"> API Log</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuracion/logger') }}"> System Log</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuracion/usertrackinglogger') }}"> User
                            Tracking</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</div>
