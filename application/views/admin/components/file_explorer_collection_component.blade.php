<script type="text/x-template" id="fileExplorerCollection-template">
<div class="panel dashboard-list-panel fileExplorerCollection-root">
    <div class="title panel-title-row">
        <h5><a href="{{base_url('admin/files')}}">{{ lang('dashboard_files_title') }}</a></h5>
        <span class="dash-files__count">@{{typeof total === 'number' ? total : files.length}} {{ lang('dashboard_files_total') }}</span>
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
                <img v-if="isImage(file)" :src="file.get_full_file_path()" :alt="file.get_filename()">
                <i v-else class="material-icons">@{{ fileIcon(file) }}</i>
            </span>
            <span class="dash-files-tile__name truncate">@{{ file.get_filename() }}</span>
            <button
                type="button"
                class="dash-files-tile__star dashboard-icon-btn"
                :class="{ 'is-on': file.featured == '1' }"
                aria-label="{{ lang('dashboard_files_share') }}"
                @click.prevent="featuredFileServe(file)"
            >
                <i class="material-icons">grade</i>
            </button>
        </a>
    </div>
    <div v-else class="dashboard-empty">
        <i class="material-icons">folder_open</i>
        <p>{{ lang('dashboard_files_empty') }}</p>
        @if(has_permisions('CREATE_FILE'))
        <a href="{{base_url('admin/files')}}" class="btn-small waves-effect waves-light">{{ lang('dashboard_files_empty_cta') }}</a>
        @endif
    </div>
</div>
</script>
