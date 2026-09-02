<div v-show="sectionActive == 'backups'" class="database-manager">
    <div class="config-section-header config-section-header--split">
        <div>
            <h2 class="page-header">{{ lang('config_database_title') }}</h2>
            <p class="section-description">{{ lang('config_database_desc') }}</p>
        </div>
        <button class="btn waves-effect waves-light btn-accent"
                @click="createDatabaseBackup()"
                :disabled="creatingBackup">
            <i class="material-icons left">backup</i>
            <span v-if="!creatingBackup">{{ lang('config_create_backup') }}</span>
            <span v-else>{{ lang('config_creating') }}</span>
        </button>
    </div>

    <div class="row stats-row">
        <div class="col s12 m4">
            <div class="card z-depth-1 stats-card">
                <div class="card-content">
                    <div class="stats-icon">
                        <i class="material-icons" aria-hidden="true">folder_special</i>
                    </div>
                    <div class="stats-info">
                        <span class="stats-label">{{ lang('config_total_backups') }}</span>
                        <p class="stats-value">@{{ files.length }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card z-depth-1 stats-card">
                <div class="card-content">
                    <div class="stats-icon teal">
                        <i class="material-icons" aria-hidden="true">schedule</i>
                    </div>
                    <div class="stats-info">
                        <span class="stats-label">{{ lang('config_last_backup') }}</span>
                        <p class="stats-value">@{{ lastBackupDate }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card z-depth-1 stats-card">
                <div class="card-content">
                    <div class="stats-icon accent">
                        <i class="material-icons" aria-hidden="true">data_usage</i>
                    </div>
                    <div class="stats-info">
                        <span class="stats-label">{{ lang('config_total_size') }}</span>
                        <p class="stats-value">@{{ totalSize }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card z-depth-1 backups-card">
        <div class="card-content">
            <div class="card-title-wrapper">
                <span class="widget-title">
                    <i class="material-icons" aria-hidden="true">history</i>
                    {{ lang('config_backup_history') }}
                </span>
                <div class="search-wrapper" v-if="files.length > 5">
                    <i class="material-icons prefix" aria-hidden="true">search</i>
                    <input type="search" v-model="searchQuery" placeholder="{{ lang('config_search_backup') }}" aria-label="{{ lang('search') }}">
                </div>
            </div>

            <div v-if="!files.length" class="config-empty">
                <i class="material-icons" aria-hidden="true">cloud_off</i>
                <p class="page-header">{{ lang('config_no_backups_title') }}</p>
                <p>{{ lang('config_no_backups_desc') }}</p>
                <button class="btn waves-effect waves-light btn-accent" @click="createDatabaseBackup()">
                    <i class="material-icons left">add</i>
                    {{ lang('config_create_first_backup') }}
                </button>
            </div>

            <div v-else class="backups-table-wrapper">
                <table class="striped highlight responsive-table">
                    <thead>
                        <tr>
                            <th>{{ lang('file') }}</th>
                            <th>{{ lang('config_created_at') }}</th>
                            <th>{{ lang('config_location') }}</th>
                            <th>{{ lang('config_size') }}</th>
                            <th class="center-align">{{ lang('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(file, index) in filteredFiles" :key="file.filename || index">
                            <td>
                                <div class="file-info">
                                    <i class="material-icons file-icon" aria-hidden="true">description</i>
                                    <span class="file-name">@{{ file.filename }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="date-badge">@{{ formatDate(file.date_create) }}</span>
                            </td>
                            <td>
                                <code class="path-code">@{{ file.file_path }}</code>
                            </td>
                            <td>
                                <span class="size-badge">@{{ formatFileSize(file.file_size) }}</span>
                            </td>
                            <td class="center-align">
                                <div class="action-buttons">
                                    <a :href="file.download_url"
                                       class="btn-flat btn-small waves-effect tooltipped"
                                       data-position="top"
                                       data-tooltip="{{ lang('config_download') }}"
                                       :aria-label="'{{ lang('config_download') }}'">
                                        <i class="material-icons">file_download</i>
                                    </a>
                                    <button type="button" @click="confirmDelete(file)"
                                            class="btn-flat btn-small waves-effect tooltipped"
                                            data-position="top"
                                            data-tooltip="{{ lang('delete') }}"
                                            :aria-label="'{{ lang('delete') }}'">
                                        <i class="material-icons red-text">delete</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteBackupModal" class="modal">
        <div class="modal-content">
            <h2 class="page-header">{{ lang('confirm_delete') }}</h2>
            <p>{{ lang('config_confirm_delete_backup') }}</p>
            <p v-if="fileToDelete"><strong>@{{ fileToDelete.filename }}</strong></p>
            <p class="red-text">{{ lang('config_delete_undone') }}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="modal-close waves-effect btn-flat">{{ lang('cancel') }}</button>
            <button type="button" @click="deleteFile(fileToDelete)" class="waves-effect btn red">
                <i class="material-icons left">delete</i>
                {{ lang('delete') }}
            </button>
        </div>
    </div>
</div>
