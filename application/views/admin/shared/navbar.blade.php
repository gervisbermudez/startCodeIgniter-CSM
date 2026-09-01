<nav class="main-navbar">
    <div class="nav-wrapper">
        <button
            type="button"
            class="search-top-trigger"
            id="navbar-search-trigger"
            data-search-palette-trigger
            aria-label="{{ lang('search') }}"
            aria-haspopup="dialog"
        >
            <i class="material-icons" aria-hidden="true">search</i>
            <span class="search-top-trigger__label hide-on-small-only">{{ lang('search_placeholder') }}</span>
            <kbd class="search-top-kbd hide-on-small-only">{{ lang('search_shortcut_hint') }}</kbd>
        </button>
        <a href="#" data-target="slide-out" class="sidenav-trigger show-on-medium-and-down" aria-label="{{ lang('menu_expand') }}"><i
                class="material-icons" aria-hidden="true">menu</i></a>
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
                <a class='dropdown-trigger' href='#' data-target='notifications-list' aria-label="{{ lang('notifications_bell') }}">
                    <span v-show="notifications.length" v-cloak class="new badge"
                        data-badge-caption="">@{{notifications.length}}</span>
                    <i class="material-icons" aria-hidden="true">notifications</i>
                </a>
            </div>
            <ul id='notifications-list' class='dropdown-content notifications-dropdown'>
                <li v-for="(notification, index) in notifications" :key="notification.notification_id" class="notifications-dropdown-item">
                    <a href="#!"
                        :title="notification.description"
                        :aria-label="notification.title"
                        v-on:click.prevent="openNotification(notification, index, $event)">
                        <i class="material-icons" aria-hidden="true">notifications</i>
                        <div>
                            <span class="title"><b>@{{notification.title}}</b></span>
                        </div>
                        <div>
                            <span class="message">@{{getcontentText(notification.description, 60)}}</span>
                        </div>
                    </a>
                    <i
                        class="material-icons notifications-mark tooltipped"
                        data-position="left"
                        data-tooltip="{{ lang('notifications_mark_read') }}"
                        aria-label="{{ lang('notifications_mark_read') }}"
                        v-on:click.stop.prevent="markRead(notification, index, false)"
                    >done</i>
                </li>
                <li v-if="!notifications.length">
                    <a href="#!">
                        <i class="material-icons" aria-hidden="true">notifications_none</i>
                        <div>
                            <span class="title"><b>{{ lang('notifications_empty') }}</b></span>
                        </div>
                        <div>
                            <span class="message">{{ lang('notifications_empty_hint') }}</span>
                        </div>
                    </a>
                </li>
                <li class="divider" tabindex="-1"></li>
                <li class="notifications-view-all">
                    <a href="{{ base_url('admin/notifications') }}">{{ lang('notifications_view_all') }}</a>
                </li>
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
                @if(has_permisions('UPDATE_DASHBOARD_LAYOUT'))
                <li>
                    <a href="{{ base_url('admin') }}?customize=1"
                       onclick="if (window.DashboardModule && typeof DashboardModule.startEditLayout === 'function') { DashboardModule.startEditLayout(); var t=document.querySelector('a.dropdown-trigger[data-target=user_dropdown]'); var i=t && window.M && M.Dropdown.getInstance(t); if (i) { i.close(); } return false; }">
                        <i class="material-icons">dashboard</i>{{ lang('dashboard_layout_edit') }}
                    </a>
                </li>
                @endif
                @if(has_permisions('SELECT_ANALYTICS'))
                <li>
                    <a href="{{ base_url('admin/analytics') }}">
                        <i class="material-icons">assessment</i>{{ lang('dashboard_view_analytics') }}
                    </a>
                </li>
                @endif
                <li><a href="{{ base_url('admin/configuration') }}"><i class="material-icons">settings</i>
                        Settings</a></li>
                <li><a target="_blank" href="{{ base_url() }}"><i class="material-icons">launch</i> View site</a></li>
                <li><a href="{{ base_url('admin/login/') }}"> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
