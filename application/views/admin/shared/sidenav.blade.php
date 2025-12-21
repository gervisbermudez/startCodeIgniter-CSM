<div class="sidemenu">
    <a href="{{ base_url('admin/') }}" class="brand-logo">{{ADMIN_BRAND_NAME}}</a>
    <a href="#" class="sidenav-trigger-lg hide-on-med-and-down"><i class="material-icons">menu</i></a>
    <ul id="slide-out" class="sidenav collapsible">
        <li class="show-on-medium-and-down">
            <a class="waves-effect" href="{{ base_url('admin') }}"><i class="material-icons">dashboard</i>
                {{ lang('menu_dashboard') }}</a>
        </li>
        <li class="{{isSectionActive('users')}}">
            <div class="collapsible-header">
                <i class="material-icons">people</i> <span>{{ lang('menu_users') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_USERS'))
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/users/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/users/usergroups') }}">{{ lang('menu_usergroups') }}</a>
                    </li>
                    @if(has_permisions('CREATE_USER'))
                    <li>
                        <a href="{{ base_url('admin/users/add/') }}">{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('pages')}}">
            <div class="collapsible-header">
                <i class="material-icons">web</i>
                <span>{{ lang('menu_pages') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_PAGES'))
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/pages/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_PAGE'))
                    <li>
                        <a href="{{ base_url('admin/pages/new/') }}">{{ lang('menu_new') }}</a>
                    </li>
                    @endif

                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('siteforms')}}">
            <div class="collapsible-header">
                <i class="material-icons">assistant</i>
                <span>{{ lang('menu_siteforms') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/siteforms') }}">{{ lang('menu_all') }}</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/siteforms/new/') }}">{{ lang('menu_new') }}</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/siteforms/submit/') }}">{{ lang('menu_submissions') }}</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('calendar')}}">
            <a class="waves-effect" href="{{ base_url('admin/calendar') }}"><i class="material-icons">event_note</i>
                {{ lang('menu_calendar') }}</a>
        </li>
        <li class="{{isSectionActive('analytics')}}">
            <a class="waves-effect" href="{{ base_url('admin/configuration/analytics') }}"><i class="material-icons">assessment</i>
                {{ lang('menu_analytics') }}</a>
        </li>
        <li class="{{isSectionActive('fragments')}}">
        <li class="{{isSectionActive('fragments')}}">
            <div class="collapsible-header">
                <i class="material-icons">bookmark_border</i>
                <span>{{ lang('menu_fragments') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a href="{{ base_url('admin/fragments/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/fragments/new/') }}">{{ lang('menu_new') }}</a>
                    </li>
                </ul>
            </div>
        </li>
        @if(has_permisions('SELECT_FILES'))
        <li class="{{isSectionActive('files')}}">
            <a class="waves-effect" href="{{ base_url('admin/files') }}"><i
                    class="material-icons">markunread_mailbox</i>
                {{ lang('menu_files') }}</a>
        </li>
        @endif
        @if(has_permisions('SELECT_MENUS'))
        <li class="{{isSectionActive('menus')}}">
            <div class="collapsible-header">
                <i class="material-icons">menu</i>
                <span>{{ lang('menu_menus') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>

                    <li>
                        <a href="{{ base_url('admin/menus/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    @if(has_permisions('CREATE_MENU'))
                    <li>
                        <a href="{{ base_url('admin/menus/new/') }}">{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        <li class="{{isSectionActive('categories')}}">
            <div class="collapsible-header">
                <i class="material-icons">receipt</i>
                <span>{{ lang('menu_categories') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_CATEGORIES'))
                    <li>
                        <a href="{{ base_url('admin/categories/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_CATEGORIE'))
                    <li>
                        <a href="{{ base_url('admin/categories/new/') }}">{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('events')}}">
            <div class="collapsible-header">
                <i class="material-icons">assistant</i>
                <span>{{ lang('menu_events') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/events/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/events/add/') }}">{{ lang('menu_new') }}</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('gallery')}}">
            <div class="collapsible-header">
                <i class="material-icons">perm_media</i>
                <span>{{ lang('menu_albums') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/gallery') }}">{{ lang('menu_all') }}</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/gallery/new/') }}">{{ lang('menu_new_album') }}</a>
                    </li>
                </ul>
            </div>

        </li>
        <li class="{{isSectionActive('videos')}}">
            <div class="collapsible-header">
                <i class="material-icons">video_library</i>
                <span>{{ lang('menu_videos') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/videos') }}">{{ lang('menu_all') }}</a>
                    </li>
                    <li>
                        <a href="{{ base_url('admin/videos/new/') }}">{{ lang('menu_create_video') }}</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="{{isSectionActive('admin/custommodels', 'match')}}">
            <div class="collapsible-header">
                <i class="material-icons">assessment</i>
                <span>{{ lang('menu_models') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    @if(has_permisions('SELECT_FORM_CUSTOMS'))
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/custommodels/') }}">{{ lang('menu_all') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('CREATE_FORM_CUSTOM'))
                    <li>
                        <a href="{{ base_url('admin/custommodels/new') }}">{{ lang('menu_new') }}</a>
                    </li>
                    @endif
                    @if(has_permisions('SELECT_CONTENT_DATA'))
                    <li class="{{isSectionActive('admin/custommodels', 'match')}}">
                        <a class="waves-effect" href="{{ base_url('admin/custommodels/content') }}">
                            {{ lang('menu_contents') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        <li class="{{isSectionActive('configuration')}}">
            <div class="collapsible-header">
                <i class="material-icons">settings</i>
                <span>{{ lang('menu_settings') }}</span>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration') }}">{{ lang('menu_configuration') }}</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/import') }}">{{ lang('menu_import') }}</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/export') }}">{{ lang('menu_export') }}</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/apilogger') }}">{{ lang('menu_api_log') }}</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/logger') }}">{{ lang('menu_system_log') }}</a>
                    </li>
                    <li>
                        <a class="waves-effect" href="{{ base_url('admin/configuration/usertrackinglogger') }}">{{ lang('menu_user_tracking') }}</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</div>
