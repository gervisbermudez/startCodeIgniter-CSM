<div v-show="sectionActive == 'export'">
    <div class="config-section-header">
        <h2 class="page-header">{{ lang('config_export') }}</h2>
    </div>
    <div class="center" v-show="loader">
        <preloader />
    </div>
    <div class="card z-depth-1" v-show="!loader">
        <div class="card-content">
            <ul class="collapsible">
                <li>
                    <div class="toggle-all-data">
                        <label>
                            <input type="checkbox" v-on:change="toggleData(exportData.pages, 'pages')" />
                            <span></span>
                        </label>
                    </div>
                    <div class="collapsible-header"><i class="material-icons">web</i>{{ lang('config_pages') }}</div>
                    <div class="collapsible-body">
                        <ul class="collection">
                            <li class="collection-item avatar" v-for="(page, index) in exportData.pages" :key="'ep-' + index">
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
                <li>
                    <div class="toggle-all-data">
                        <label>
                            <input type="checkbox" v-on:change="toggleData(exportData.config, 'config')" />
                            <span></span>
                        </label>
                    </div>
                    <div class="collapsible-header"><i class="material-icons">settings</i> {{ lang('menu_configuration') }}</div>
                    <div class="collapsible-body">
                        <ul class="collection">
                            <li class="collection-item avatar" v-for="(item, index) in exportData.config" :key="'ec-' + index">
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
            <button type="button" class="btn waves-effect waves-light btn-accent" @click="generateFile()" :disabled="!btnEnable">
                <i class="material-icons left">file_download</i> {{ lang('config_export') }}
            </button>
        </div>
    </div>
</div>
