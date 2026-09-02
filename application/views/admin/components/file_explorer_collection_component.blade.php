<script type="text/x-template" id="fileExplorerCollection-template">
<div class="dash-list-widget fileExplorerCollection-root has-deco">
    <div class="dash-widget-head">
        <div class="dash-widget-head__lead">
            <span class="dash-widget-glyph" aria-hidden="true"><i class="material-icons">folder</i></span>
            <h5><a href="{{ base_url('admin/files') }}">{{ lang('dashboard_files_title') }}</a></h5>
        </div>
        <div class="dash-widget-head__tools">
            <span class="dash-widget-head__count">@{{typeof total === 'number' ? total : files.length}} {{ lang('dashboard_files_total') }}</span>
            @include('admin.components.dash_widget_add', [
                'perm' => 'CREATE_FILE',
                'href' => base_url('admin/files'),
                'tip' => lang('tooltip_new_file'),
            ])
        </div>
    </div>
    <div v-if="shortFiles.length" class="dash-files-grid">
        <a
            v-for="(file, index) in shortFiles"
            :key="file.file_id || index"
            class="dash-files-tile"
            :href="file.get_full_file_path()"
            :title="file.get_filename()"
        >
            <span class="dash-files-tile__media" :class="{ 'is-image': isImage(file) }">
                <img v-if="isImage(file)" :src="file.get_full_file_path()" alt="">
                <i v-else class="material-icons">@{{ fileIcon(file) }}</i>
            </span>
            <span class="dash-files-tile__name truncate">@{{ file.get_filename() }}</span>
        </a>
    </div>
    <div v-else class="dashboard-empty">
        <i class="material-icons">folder_open</i>
        <p>{{ lang('dashboard_files_empty') }}</p>
    </div>
    <img class="dash-widget-deco" src="{{ base_url('public/img/admin/dashboard/undraw_folder_files.png') }}" alt="" aria-hidden="true">
</div>
</script>
