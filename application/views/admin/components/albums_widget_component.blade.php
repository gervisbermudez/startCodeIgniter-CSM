<script type="text/x-template" id="albumes-widget-template">
    <div class="dash-list-widget albumes has-deco" style="--dash-deco: url('{{ base_url('public/img/admin/undraw/undraw_photograph_gwbm.svg') }}')">
        <div class="dash-widget-head">
            <div class="dash-widget-head__lead">
                <span class="dash-widget-glyph" aria-hidden="true"><i class="material-icons">photo_library</i></span>
                <h5><a href="{{ base_url('admin/gallery') }}">{{ lang('dashboard_albums_title') }}</a></h5>
            </div>
            <div class="dash-widget-head__tools">
                <span class="dash-widget-head__count">@{{typeof total === 'number' ? total : albumes.length}} {{ lang('dashboard_albums_total') }}</span>
                @include('admin.components.dash_widget_add', [
                    'perm' => 'CREATE_GALLERY',
                    'href' => base_url('admin/gallery/new/'),
                    'tip' => lang('tooltip_new_album'),
                ])
            </div>
        </div>
        <ul class="dash-albums" v-if="albumes.length">
            <li v-for="(album, index) in albumes" :key="index">
                <a :href="base_url('admin/gallery/items/' + album.album_id)" class="dash-albums__item">
                    <span class="dash-albums__stack">
                        <img :src="getPageImagePath(album, 0)" alt="">
                        <img :src="getPageImagePath(album, 1)" alt="">
                    </span>
                    <span class="dash-albums__name truncate">@{{album.name}}</span>
                </a>
            </li>
        </ul>
        <div v-else class="dashboard-empty">
            <i class="material-icons">photo_library</i>
            <p>{{ lang('dashboard_albums_empty') }}</p>
        </div>
    </div>
</script>
