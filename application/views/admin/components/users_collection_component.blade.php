<script type="text/x-template" id="user-collection-template">
    <div class="dash-list-widget has-deco">
        <div class="dash-widget-head">
            <div class="dash-widget-head__lead">
                <span class="dash-widget-glyph" aria-hidden="true"><i class="material-icons">people</i></span>
                <h5><a href="{{ base_url('admin/users') }}">{{ lang('dashboard_users_title') }}</a></h5>
            </div>
            <div class="dash-widget-head__tools">
                <span class="dash-widget-head__count">@{{typeof total === 'number' ? total : users.length}} {{ lang('dashboard_users_total') }}</span>
                @include('admin.components.dash_widget_add', [
                    'perm' => 'CREATE_USER',
                    'href' => base_url('admin/users/add'),
                    'tip' => lang('tooltip_new_user'),
                ])
            </div>
        </div>
        <ul class="dash-people">
            <li v-for="(user, index) in users" :key="index">
                <a :href="user.get_profileurl()" class="dash-people__row">
                    <img :src="user.get_avatarurl()" alt="" class="dash-people__avatar">
                    <div class="dash-people__meta">
                        <span class="dash-people__name truncate">@{{user.get_fullname()}}</span>
                        <span class="dash-people__role truncate">@{{user.role}}</span>
                    </div>
                </a>
            </li>
            <li v-if="users.length === 0" class="dash-list__empty">{{ lang('dashboard_users_empty') }}</li>
        </ul>
        <img class="dash-widget-deco" src="{{ base_url('public/img/admin/dashboard/undraw_browsing_online.png') }}" alt="" aria-hidden="true">
    </div>
</script>
