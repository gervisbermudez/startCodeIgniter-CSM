<nav class="main-navbar">
    <div class="nav-wrapper">
        <a href="#" data-target="slide-out" class="sidenav-trigger show-on-medium-and-down"><i class="material-icons">menu</i></a>
        <a class='dropdown-trigger right' href='#' data-target='user_dropdown'>
            @if (userdata('avatar'))
            <img src="{{base_url(IMGPROFILEPATH) . userdata('username') . '/' . userdata('avatar') }}" alt="" class="circle z-depth-1" />
            @else
            <i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
            @endif
        </a>
        <div id="user_dropdown" class="dropdown-content user-dropdown">
            <div class="user-view">
                <div class="background">
                </div>
                <a href="{{base_url('admin/usuarios/ver/' . userdata('user_id')) }}" class="user-avatar">
                    @if (userdata('avatar'))
                    <img src="{{base_url(IMGPROFILEPATH) . userdata('username') . '/' . userdata('avatar') }}" alt="" class="circle z-depth-1" />
                    @else
                    <i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
                    @endif
                </a>
                <a class="avatar-username" href="{{base_url('admin/usuarios/ver/' . userdata('user_id')) }}">
                    <span class="white-text name">{{userdata('username') }}</span>
                </a>
                <a class="avatar-email" href="#email">
                    <span class="white-text email">{{userdata('type') }}</span>
                </a>
            </div>
            <ul class="menu">
                <li class="divider" tabindex="-1"></li>
                <li><a href="{{ base_url('admin/configuracion') }}"><i class="material-icons">settings</i> Configuración</a></li>
                <li><a target="_blank" href="{{ base_url() }}"><i class="material-icons">launch</i> Ir al sitio</a></li>
                <li><a href="{{ base_url('admin/login/') }}"> Cerrar sesion</a></li>
            </ul>
        </div>
    </div>
</nav>
