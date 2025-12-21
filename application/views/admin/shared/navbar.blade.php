<nav class="main-navbar">
    <div class="nav-wrapper">
        <form method="GET" action="{{base_url('admin/search/')}}" class="input-field search-top">
            <i class="material-icons prefix">search</i>
            <input placeholder="Search..." name="q" type="text" class="validate">
        </form>
        <a href="#" data-target="slide-out" class="sidenav-trigger show-on-medium-and-down"><i
                class="material-icons">menu</i></a>
        <a class='dropdown-trigger right' href='#' data-target='user_dropdown'>
            @if (userdata('avatar'))
            <img src="{{base_url(userdata('avatar'))}}" alt="" class="circle z-depth-1" />
            @else
            <i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
            @endif
        </a>
        <!-- Switch -->
        <div class="switch right darkmode-switch">
            <label>
                <input type="checkbox" id="darkmode-switch">
                <span class="lever">
                    <i class="material-icons">brightness_4</i>
                </span>
            </label>
        </div>
        <div class="right notifications" id="notifications">
            <div class="icon-container">
                <a class='dropdown-trigger' href='#' data-target='notifications-list'>
                    <span v-show="notifications.length" v-cloak class="new badge"
                        data-badge-caption="">@{{notifications.length}}</span>
                    <i class="material-icons">notifications</i>
                </a>
            </div>
            <!-- Dropdown Structure -->
            <ul id='notifications-list' class='dropdown-content notifications-dropdown'>
                <li v-for="(notification, index) in notifications" :key="index">
                    <a @click="setArchive(notification, index)" :href="base_url(notification.url)"
                        :title="notification.description">
                        <i class="material-icons">notifications</i>
                        <div>
                            <span class="title"><b>@{{notification.title}}</b></span>
                        </div>
                        <div>
                            <span class="message">@{{getcontentText(notification.description, 60)}}</span>
                        </div>
                    </a>
                    <i @click="setArchive(notification, index)" class="material-icons">check</i>
                </li>
                <li v-if="!notifications.length">
                    <a href="#!" title="No notifications">
                        <div>
                            <span class="title"><b>No notifications</b></span>
                        </div>
                        <div>
                            <span class="message">Enjoy your day :)</span>
                        </div>
                    </a>
                    <i class="material-icons">beach_access</i>

                </li>
                <!-- <li><a href="#!">one</a></li>
                <li><a href="#!">two</a></li>
                <li class="divider" tabindex="-1"></li>
                <li><a href="#!">three</a></li>
                <li><a href="#!"><i class="material-icons">view_module</i>four</a></li>
                <li><a href="#!"><i class="material-icons">cloud</i>five</a></li> -->
            </ul>
        </div>
        <div id="user_dropdown" class="dropdown-content user-dropdown">
            <div class="user-view">
                <div class="background">
                </div>
                <a href="{{base_url('admin/users/ver/' . userdata('user_id')) }}" class="user-avatar">
                    @if (userdata('avatar'))
                    <img src="{{base_url(userdata('avatar')) }}" alt="" class="circle z-depth-1" />
                    @else
                    <i class="material-icons circle grey lighten-5 profile z-depth-1">account_circle</i>
                    @endif
                </a>
                <a class="avatar-username" href="{{base_url('admin/users/ver/' . userdata('user_id')) }}">
                    <span class="white-text name">{{userdata('username') }}</span>
                </a>
                <a class="avatar-email" href="#email">
                    <span class="white-text email">{{userdata('type') }}</span>
                </a>
            </div>
            <ul class="menu">
                <li class="divider" tabindex="-1"></li>
                <li><a href="{{ base_url('admin/configuration') }}"><i class="material-icons">settings</i>
                        Settings</a></li>
                <li><a target="_blank" href="{{ base_url() }}"><i class="material-icons">launch</i> View site</a></li>
                <li><a href="{{ base_url('admin/login/') }}"> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
