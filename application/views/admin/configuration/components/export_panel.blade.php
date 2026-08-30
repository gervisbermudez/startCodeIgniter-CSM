<div v-show="sectionActive == 'export'">
    <div class="config-section-header">
        <h2 class="page-header">{{ lang('config_export') }}</h2>
        <p class="section-description">{{ lang('config_export_desc') }}</p>
    </div>
    <div class="center" v-show="loader">
        <preloader />
    </div>
    <div class="card z-depth-1" v-show="!loader">
        <div class="card-content">
            <ul class="collapsible config-pick">
                <li>
                    <div class="collapsible-header config-pick-header">
                        <label class="config-pick-check" @click.stop aria-label="{{ lang('config_select_all') }}">
                            <input type="checkbox" class="filled-in" v-on:change="toggleData(exportData.pages, 'pages')" />
                            <span></span>
                        </label>
                        <i class="material-icons" aria-hidden="true">web</i>
                        <span class="config-pick-header__label">{{ lang('config_pages') }}</span>
                    </div>
                    <div class="collapsible-body config-pick-body">
                        <ul class="config-pick-list">
                            <li class="config-pick-row" v-for="(page, index) in exportData.pages" :key="'ep-' + index">
                                <label class="config-pick-check">
                                    <input type="checkbox" class="filled-in" v-model="page.checked" />
                                    <span></span>
                                </label>
                                <div class="config-pick-row__text">
                                    <span class="config-pick-row__title">@{{page.title}}</span>
                                    <span class="config-pick-row__meta">@{{page.path}}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header config-pick-header">
                        <label class="config-pick-check" @click.stop aria-label="{{ lang('config_select_all') }}">
                            <input type="checkbox" class="filled-in" v-on:change="toggleData(exportData.config, 'config')" />
                            <span></span>
                        </label>
                        <i class="material-icons" aria-hidden="true">settings</i>
                        <span class="config-pick-header__label">{{ lang('menu_configuration') }}</span>
                    </div>
                    <div class="collapsible-body config-pick-body">
                        <ul class="config-pick-list">
                            <li class="config-pick-row" v-for="(item, index) in exportData.config" :key="'ec-' + index">
                                <label class="config-pick-check">
                                    <input type="checkbox" class="filled-in" v-model="item.checked" />
                                    <span></span>
                                </label>
                                <div class="config-pick-row__text">
                                    <span class="config-pick-row__title">@{{item.config_label}}</span>
                                    <span class="config-pick-row__meta">@{{item.config_name}}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="config-actions">
                <button type="button" class="btn waves-effect waves-light btn-accent" @click="generateFile()" :disabled="!btnEnable">
                    <i class="material-icons left">file_download</i> {{ lang('config_export') }}
                </button>
            </div>
        </div>
    </div>
</div>
