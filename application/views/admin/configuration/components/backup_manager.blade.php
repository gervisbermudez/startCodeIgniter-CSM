<div v-show="sectionActive == 'database'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Manage database backup</h4>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p>
                    <h5>Crear backup</h5>
                    <a class="waves-effect waves-light btn amber" @click="createDatabaseBackup()"><i
                            class="material-icons left">sd_card</i> backup</a>
                    </p>
                    <p v-if="files.length">
                        Backups creados:
                    </p>
                    <ul class="collapsible" v-if="files.length">
                        <li v-for="(file, index) in files" :key="index">
                            <div class="collapsible-header"><i class="material-icons">filter_drama</i>
                                @{{file.get_filename()}}
                            </div>
                            <div class="collapsible-body">
                                <ul>
                                    <li>
                                        <b>Fecha:</b> @{{file.date_create}}
                                    </li>
                                    <li>
                                        <b>File Type:</b> @{{file.file_type}}
                                    </li>
                                    <li>
                                        <b>Path:</b> @{{file.file_path}}
                                    </li>
                                </ul>
                                <br />
                                <a class="waves-effect waves-light btn amber" :href="file.get_full_file_path()"><i
                                        class="material-icons left">file_download</i> Download</a>
                                <a class="waves-effect waves-light btn red" @click="deleteFile(file);"><i
                                        class="material-icons left">delete</i> Delete</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
