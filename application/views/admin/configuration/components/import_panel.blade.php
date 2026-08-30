<div v-show="sectionActive == 'import'">
    <div class="config-section-header">
        <h2 class="page-header">{{ lang('config_import') }}</h2>
        <p class="section-description">{{ lang('config_choose_file') }}</p>
    </div>
    <div class="center" v-show="loader">
        <preloader />
    </div>
    <div class="card z-depth-1" v-show="!loader">
        <div class="card-content">
            <div class="file-field input-field">
                <div class="btn waves-effect waves-light btn-accent">
                    <span>{{ lang('config_select_file') }}</span>
                    <input type="file" id="files" name="files[]" v-on:change="handleFileSelect" />
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
            <ul class="collapsible" v-show="selectedFile">
                <li v-if="exportData.pages.length">
                    <div class="toggle-all-data">
                        <label>
                            <input type="checkbox" v-on:change="toggleData(exportData.pages, 'pages')" />
                            <span></span>
                        </label>
                    </div>
                    <div class="collapsible-header"><i class="material-icons">web</i>{{ lang('config_pages') }}</div>
                    <div class="collapsible-body">
                        <ul class="collection">
                            <li class="collection-item avatar" v-for="(page, index) in exportData.pages" :key="'ip-' + index">
                                <div class="material-icons circle checkbox">
                                    <label>
                                        <input type="checkbox" v-model="page.checked" />
                                        <span></span>
                                    </label>
                                </div>
                                <span class="title"><b>@{{page.title}}</b></span>
                                <p>@{{page.path}}</p>
                            </li>
                        </ul>
                    </div>
                </li>
                <li v-if="exportData.config.length">
                    <div class="toggle-all-data">
                        <label>
                            <input type="checkbox" v-on:change="toggleData(exportData.config, 'config')" />
                            <span></span>
                        </label>
                    </div>
                    <div class="collapsible-header"><i class="material-icons">settings</i> {{ lang('menu_configuration') }}</div>
                    <div class="collapsible-body">
                        <ul class="collection">
                            <li class="collection-item avatar" v-for="(item, index) in exportData.config" :key="'ic-' + index">
                                <div class="material-icons circle checkbox">
                                    <label>
                                        <input type="checkbox" v-model="item.checked" />
                                        <span></span>
                                    </label>
                                </div>
                                <span class="title"><b>@{{item.config_label}}</b></span>
                                <p>@{{item.config_name}}</p>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div v-show="selectedFile">
                <button type="button" class="btn waves-effect waves-light btn-accent" @click="saveData()" :disabled="!btnEnable">
                    <i class="material-icons left">save</i> {{ lang('config_import') }}
                </button>
            </div>
        </div>
    </div>
</div>
