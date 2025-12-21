<div v-show="sectionActive == 'database'" class="database-manager">
    <!-- Header Section -->
    <div class="section-header">
        <div class="row valign-wrapper" style="margin-bottom: 0;">
            <div class="col s12 m8">
                <h4 class="section-title">
                    <i class="material-icons left">storage</i>
                    Gestión de Base de Datos
                </h4>
                <p class="section-description grey-text text-darken-1">
                    Crea, descarga y administra copias de seguridad de tu base de datos
                </p>
            </div>
            <div class="col s12 m4 right-align">
                <button class="btn-large waves-effect waves-light gradient-primary" 
                        @click="createDatabaseBackup()"
                        :disabled="creatingBackup">
                    <i class="material-icons left">backup</i>
                    <span v-if="!creatingBackup">Crear Backup</span>
                    <span v-else>Creando...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row stats-row">
        <div class="col s12 m4">
            <div class="card stats-card hoverable">
                <div class="card-content">
                    <div class="stats-icon blue">
                        <i class="material-icons">folder_special</i>
                    </div>
                    <div class="stats-info">
                        <span class="stats-label">Total de Backups</span>
                        <h5 class="stats-value">@{{ files.length }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card stats-card hoverable">
                <div class="card-content">
                    <div class="stats-icon green">
                        <i class="material-icons">schedule</i>
                    </div>
                    <div class="stats-info">
                        <span class="stats-label">Último Backup</span>
                        <h5 class="stats-value">@{{ lastBackupDate }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m4">
            <div class="card stats-card hoverable">
                <div class="card-content">
                    <div class="stats-icon orange">
                        <i class="material-icons">data_usage</i>
                    </div>
                    <div class="stats-info">
                        <span class="stats-label">Espacio Total</span>
                        <h5 class="stats-value">@{{ totalSize }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backups List -->
    <div class="row">
        <div class="col s12">
            <div class="card backups-card">
                <div class="card-content">
                    <div class="card-title-wrapper">
                        <span class="card-title">
                            <i class="material-icons left">history</i>
                            Historial de Backups
                        </span>
                        <div class="search-wrapper" v-if="files.length > 5">
                            <i class="material-icons prefix">search</i>
                            <input type="text" v-model="searchQuery" placeholder="Buscar backup...">
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!files.length" class="empty-state">
                        <i class="material-icons">cloud_off</i>
                        <h5>No hay backups disponibles</h5>
                        <p>Crea tu primer backup de base de datos para comenzar</p>
                        <button class="btn waves-effect waves-light blue" @click="createDatabaseBackup()">
                            <i class="material-icons left">add</i>
                            Crear Primer Backup
                        </button>
                    </div>

                    <!-- Backups Table -->
                    <div v-else class="backups-table-wrapper">
                        <table class="striped highlight responsive-table">
                            <thead>
                                <tr>
                                    <th><i class="material-icons tiny">insert_drive_file</i> Archivo</th>
                                    <th><i class="material-icons tiny">event</i> Fecha de Creación</th>
                                    <th><i class="material-icons tiny">folder_open</i> Ubicación</th>
                                    <th><i class="material-icons tiny">straighten</i> Tamaño</th>
                                    <th class="center-align">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(file, index) in filteredFiles" :key="index" class="backup-row">
                                    <td>
                                        <div class="file-info">
                                            <i class="material-icons file-icon">description</i>
                                            <span class="file-name">@{{ file.get_filename() }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="date-badge">
                                            <i class="material-icons tiny">access_time</i>
                                            @{{ formatDate(file.date_create) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="path-code">@{{ file.file_path }}</code>
                                    </td>
                                    <td>
                                        <span class="size-badge">@{{ formatFileSize(file.file_size) }}</span>
                                    </td>
                                    <td class="center-align">
                                        <div class="action-buttons">
                                            <a :href="file.get_full_file_path()" 
                                               class="btn-floating btn-small waves-effect waves-light blue tooltipped"
                                               data-position="top" 
                                               data-tooltip="Descargar">
                                                <i class="material-icons">file_download</i>
                                            </a>
                                            <button @click="confirmDelete(file)" 
                                                    class="btn-floating btn-small waves-effect waves-light red tooltipped"
                                                    data-position="top" 
                                                    data-tooltip="Eliminar">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteBackupModal" class="modal">
        <div class="modal-content">
            <h4><i class="material-icons left red-text">warning</i>Confirmar Eliminación</h4>
            <p>¿Estás seguro de que deseas eliminar este backup?</p>
            <p v-if="fileToDelete"><strong>@{{ fileToDelete.get_filename() }}</strong></p>
            <p class="red-text"><i class="material-icons tiny">info</i> Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="modal-close waves-effect waves-light btn-flat">Cancelar</button>
            <button @click="deleteFile(fileToDelete)" class="waves-effect waves-light btn red">
                <i class="material-icons left">delete</i>
                Eliminar
            </button>
        </div>
    </div>
</div>
