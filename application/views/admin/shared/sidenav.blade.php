<div class="sidemenu">
    <a href="{{ base_url('admin/') }}" class="brand-logo">{{ADMIN_BRAND_NAME}}</a>
    <a href="#"
        class="sidenav-trigger-lg hide-on-med-and-down"
        aria-controls="slide-out"
        aria-expanded="true"
        data-label-expand="{{ lang('menu_expand') }}"
        data-label-collapse="{{ lang('menu_collapse') }}"
        aria-label="{{ lang('menu_collapse') }}">
        <i class="material-icons" aria-hidden="true">menu</i>
    </a>
    <ul id="slide-out" class="sidenav collapsible" role="navigation" aria-label="{{ lang('menu_admin') }}">
        <li class="show-on-medium-and-down {{ isSectionActive('admin') }}">
            <a class="waves-effect" href="{{ base_url('admin') }}" {!! navCurrentAttr('admin') !!}>
                <i class="material-icons" aria-hidden="true">dashboard</i>
                <span>{{ lang('menu_dashboard') }}</span>
            </a>
        </li>
        @if(has_permisions('SELECT_USERS') || has_permisions('SELECT_USERGROUPS') || has_permisions('CREATE_USER'))
        <li class="{{ isSectionActive('users') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">people</i>
                <span>{{ lang('menu_users') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_USERS'))
                    <li class="{{ isNavItemActive(array('admin/users', 'admin/users/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/users/') }}" {!! navCurrentAttr(array('admin/users', 'admin/users/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('SELECT_USERGROUPS'))
                    <li class="{{ isNavItemActive('admin/users/usergroups*') }}">
                        <a class="waves-effect" href="{{ base_url('admin/users/usergroups') }}" {!! navCurrentAttr('admin/users/usergroups*') !!}>{{ lang('menu_usergroups') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_USER'))
                    <li class="{{ isNavItemActive('admin/users/add') }}">
                        <a class="waves-effect" href="{{ base_url('admin/users/add/') }}" {!! navCurrentAttr('admin/users/add') !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_PAGES') || has_permisions('CREATE_PAGE'))
        <li class="{{ isSectionActive('pages') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">web</i>
                <span>{{ lang('menu_pages') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_PAGES'))
                    <li class="{{ isNavItemActive(array('admin/pages', 'admin/pages/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/pages/') }}" {!! navCurrentAttr(array('admin/pages', 'admin/pages/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_PAGE'))
                    <li class="{{ isNavItemActive('admin/pages/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/pages/new/') }}" {!! navCurrentAttr('admin/pages/new') !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_SITEFORMS'))
        <li class="{{ isSectionActive('siteforms') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">assignment</i>
                <span>{{ lang('menu_siteforms') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li class="{{ isNavItemActive(array('admin/siteforms', 'admin/siteforms/edit*', 'admin/siteforms/stats*', 'admin/siteforms/export*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/siteforms') }}" {!! navCurrentAttr(array('admin/siteforms', 'admin/siteforms/edit*', 'admin/siteforms/stats*', 'admin/siteforms/export*')) !!}>{{ lang('menu_siteforms_all') }}</a>
                    </li>
                    @if(has_permisions('CREATE_SITEFORM'))
                    <li class="{{ isNavItemActive('admin/siteforms/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/siteforms/new/') }}" {!! navCurrentAttr('admin/siteforms/new') !!}>{{ lang('menu_siteforms_new') }}</a>
                    </li>
                    @endif
                    <li class="{{ isNavItemActive('admin/siteforms/submit*') }}">
                        <a class="waves-effect" href="{{ base_url('admin/siteforms/submit/') }}" {!! navCurrentAttr('admin/siteforms/submit*') !!}>{{ lang('menu_siteforms_submissions') }}</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_ANALYTICS'))
        <li class="{{ isSectionActive('analytics') }}">
            <a class="waves-effect" href="{{ base_url('admin/analytics') }}" {!! navCurrentAttr('admin/analytics*') !!}>
                <i class="material-icons" aria-hidden="true">assessment</i>
                <span>{{ lang('menu_analytics') }}</span>
            </a>
        </li>
        @endif
        @if(has_permisions('SELECT_FRAGMENTS'))
        <li class="{{ isSectionActive('fragments') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">bookmark_border</i>
                <span>{{ lang('menu_fragments') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li class="{{ isNavItemActive(array('admin/fragments', 'admin/fragments/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/fragments/') }}" {!! navCurrentAttr(array('admin/fragments', 'admin/fragments/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @if(has_permisions('CREATE_FRAGMENT'))
                    <li class="{{ isNavItemActive('admin/fragments/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/fragments/new/') }}" {!! navCurrentAttr('admin/fragments/new') !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_FILES'))
        <li class="{{ isSectionActive('files') }}">
            <a class="waves-effect" href="{{ base_url('admin/files') }}" {!! navCurrentAttr('admin/files*') !!}>
                <i class="material-icons" aria-hidden="true">folder</i>
                <span>{{ lang('menu_files') }}</span>
            </a>
        </li>
        @endif
        @if(has_permisions('SELECT_MENUS'))
        <li class="{{ isSectionActive('menus') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">view_list</i>
                <span>{{ lang('menu_menus') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li class="{{ isNavItemActive(array('admin/menus', 'admin/menus/edit*', 'admin/menus/editar*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/menus/') }}" {!! navCurrentAttr(array('admin/menus', 'admin/menus/edit*', 'admin/menus/editar*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @if(has_permisions('CREATE_MENU'))
                    <li class="{{ isNavItemActive(array('admin/menus/new', 'admin/menus/nuevo')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/menus/new/') }}" {!! navCurrentAttr(array('admin/menus/new', 'admin/menus/nuevo')) !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_CATEGORIES') || has_permisions('CREATE_CATEGORIE'))
        <li class="{{ isSectionActive('categories') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">receipt</i>
                <span>{{ lang('menu_categories') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_CATEGORIES'))
                    <li class="{{ isNavItemActive(array('admin/categories', 'admin/categories/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/categories/') }}" {!! navCurrentAttr(array('admin/categories', 'admin/categories/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_CATEGORIE'))
                    <li class="{{ isNavItemActive('admin/categories/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/categories/new/') }}" {!! navCurrentAttr('admin/categories/new') !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_EVENTS') || has_permisions('SELECT_CALENDAR'))
        <li class="{{ isSectionActive('events') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">event</i>
                <span>{{ lang('menu_events') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_EVENTS'))
                    <li class="{{ isNavItemActive(array('admin/events', 'admin/events/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/events/') }}" {!! navCurrentAttr(array('admin/events', 'admin/events/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('SELECT_CALENDAR'))
                    <li class="{{ isNavItemActive(array('admin/events/calendar', 'admin/calendar')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/events/calendar') }}" {!! navCurrentAttr(array('admin/events/calendar', 'admin/calendar')) !!}>{{ lang('menu_calendar') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_EVENT'))
                    <li class="{{ isNavItemActive('admin/events/add') }}">
                        <a class="waves-effect" href="{{ base_url('admin/events/add/') }}" {!! navCurrentAttr('admin/events/add') !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_GALLERY'))
        <li class="{{ isSectionActive('gallery') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">perm_media</i>
                <span>{{ lang('menu_albums') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li class="{{ isNavItemActive(array('admin/gallery', 'admin/gallery/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/gallery') }}" {!! navCurrentAttr(array('admin/gallery', 'admin/gallery/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @if(has_permisions('CREATE_GALLERY'))
                    <li class="{{ isNavItemActive('admin/gallery/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/gallery/new/') }}" {!! navCurrentAttr('admin/gallery/new') !!}>{{ lang('menu_new_album') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_VIDEOS'))
        <li class="{{ isSectionActive('videos') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">video_library</i>
                <span>{{ lang('menu_videos') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li class="{{ isNavItemActive(array('admin/videos', 'admin/videos/edit*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/videos') }}" {!! navCurrentAttr(array('admin/videos', 'admin/videos/edit*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @if(has_permisions('CREATE_VIDEO'))
                    <li class="{{ isNavItemActive('admin/videos/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/videos/new/') }}" {!! navCurrentAttr('admin/videos/new') !!}>{{ lang('menu_create_video') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_FORM_CUSTOMS') || has_permisions('CREATE_FORM_CUSTOM'))
        <li class="{{ isSectionActive('custommodels') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">view_module</i>
                <span>{{ lang('menu_collections') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_FORM_CUSTOMS'))
                    <li class="{{ isNavItemActive(array('admin/custommodels', 'admin/custommodels/edit*', 'admin/custommodels/editForm*', 'admin/custommodels/items*', 'admin/custommodels/addData*', 'admin/custommodels/editData*')) }}">
                        <a class="waves-effect" href="{{ base_url('admin/custommodels/') }}" {!! navCurrentAttr(array('admin/custommodels', 'admin/custommodels/edit*', 'admin/custommodels/editForm*', 'admin/custommodels/items*', 'admin/custommodels/addData*', 'admin/custommodels/editData*')) !!}>{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_FORM_CUSTOM'))
                    <li class="{{ isNavItemActive('admin/custommodels/new') }}">
                        <a class="waves-effect" href="{{ base_url('admin/custommodels/new') }}" {!! navCurrentAttr('admin/custommodels/new') !!}>{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if(has_permisions('SELECT_CONFIG'))
        <li class="{{ isSectionActive('configuration') }}">
            <div class="collapsible-header waves-effect">
                <i class="material-icons" aria-hidden="true">settings</i>
                <span>{{ lang('menu_settings') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li class="{{ configNavCurrent('index', 'home') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}" {!! configNavCurrentAttr('index', 'home') !!}>{{ lang('dashboard_overview') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('index', 'general') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}?section=general" {!! configNavCurrentAttr('index', 'general') !!}>{{ lang('config_general') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('index', 'theme') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}?section=theme" {!! configNavCurrentAttr('index', 'theme') !!}>{{ lang('config_appearance') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('index', 'seo') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}?section=seo" {!! configNavCurrentAttr('index', 'seo') !!}>{{ lang('config_seo') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('index', 'integrations') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}?section=integrations" {!! configNavCurrentAttr('index', 'integrations') !!}>{{ lang('config_integrations') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('index', 'system') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}?section=system" {!! configNavCurrentAttr('index', 'system') !!}>{{ lang('config_system') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('index', 'updater') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}?section=updater" {!! configNavCurrentAttr('index', 'updater') !!}>{{ lang('config_updates') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('data', 'backups') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration/data') }}" {!! configNavCurrentAttr('data', 'backups') !!}>{{ lang('config_data_backups') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('data', 'import') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration/data') }}?section=import" {!! configNavCurrentAttr('data', 'import') !!}>{{ lang('config_import') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('data', 'export') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration/data') }}?section=export" {!! configNavCurrentAttr('data', 'export') !!}>{{ lang('config_export') }}</a>
                    </li>
                    <li class="{{ configNavCurrent('logs') }}">
                        <a class="waves-effect" href="{{ base_url('admin/configuration/logs') }}" {!! configNavCurrentAttr('logs') !!}>{{ lang('menu_logs') }}</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif
    </ul>
</div>
