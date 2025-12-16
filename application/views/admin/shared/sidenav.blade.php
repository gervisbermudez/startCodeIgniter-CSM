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
                        <a class="waves-effect" href="{{ base_url('admin/users/') }}">Todos</a>
                    </li>
                    @endif
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/users/usergroups') }}">Grupos</a>
                    </li>
                    @if(has_permisions('CREATE_USER'))
                    <li>
                        <a href="{{ base_url('admin/users/agregar/') }}">Nuevo</a>
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
                        <a class="waves-effect" href="{{ base_url('admin/pages/') }}">Todas</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_PAGE'))
                    <li>
                        <a href="{{ base_url('admin/pages/nueva/') }}">Nueva</a>
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
                        <a class="waves-effect" href="{{ base_url('admin/SiteForms') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/SiteForms/nuevo/') }}">Nuevo</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/SiteForms/submit/') }}">Recibidos</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('calendario')}}">
            <a class="waves-effect" href="{{ base_url('admin/calendar') }}"><i class="material-icons">event_note</i>
                Calendario</a>
        </li>
        <li class="{{isSectionActive('Fragments')}}">
            <a class="waves-effect" href="{{ base_url('admin/Fragments/') }}"><i
                    class="material-icons">bookmark_border</i>
                Fragmentos</a>
        </li>
        @if(has_permisions('SELECT_FILES'))
        <li class="{{isSectionActive('archivos')}}">
            <a class="waves-effect" href="{{ base_url('admin/files') }}"><i
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
                        <a href="{{ base_url('admin/categories/') }}">Todas</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_CATEGORIE'))
                    <li>
                        <a href="{{ base_url('admin/categories/nueva/') }}">Nueva</a>
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
                        <a class="waves-effect" href="{{ base_url('admin/events/') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/events/agregar/') }}">Nuevo</a>
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
                        <a class="waves-effect" href="{{ base_url('admin/gallery') }}"> Todos</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/gallery/nuevo/') }}">Nuevo Album</a>
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
                        <a class="waves-effect" href="{{ base_url('admin/CustomModels/') }}">Todos</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_FORM_CUSTOM'))
                    <li>
                        <a href="{{ base_url('admin/CustomModels/nuevo') }}">Nuevo</a>
                    </li>
                    @endif
                    @if(has_permisions('SELECT_CONTENT_DATA'))
                    <li class="{{isSectionActive('admin/custommodels', 'match')}}">
                        <a class="waves-effect" href="{{ base_url('admin/CustomModels/content') }}">
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
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}"> Configuración</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/import') }}"> Importar</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/export') }}"> Exportar</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/apilogger') }}"> API Log</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/logger') }}"> System Log</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/usertrackinglogger') }}"> User
                            Tracking</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</div>
