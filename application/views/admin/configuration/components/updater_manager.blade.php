<div v-show="sectionActive == 'updater'" class="container form">
            <div class="config-section-header">
                <h2 class="page-header">{{ lang('config_updater_manager') }}</h2>
            </div>
            <div class="row" v-if="getConfig('UPDATER_LAST_CHECK_UPDATE')">
                <div class="col s12">
                    <p>
                        <b>{{ lang('config_last_check') }}</b>: @{{getConfig('UPDATER_LAST_CHECK_UPDATE').config_value}}
                    </p>
                    <div v-if="updaterInfo">
                        <p class="subtitle">{{ lang('config_current_version') }}</p>
                        <ul class="collection">
                            <li class="collection-item"><b>{{ lang('name') }}</b>: @{{updaterInfo.local.name}}</li>
                            <li class="collection-item"><b>{{ lang('description') }}</b>: @{{updaterInfo.local.description}}</li>
                            <li class="collection-item"><b>{{ lang('config_theme_version') }}</b>: @{{updaterInfo.local.version}}</li>
                            <li class="collection-item"><b>{{ lang('config_theme_updated') }}</b>: @{{updaterInfo.local.updated}}</li>
                            <li class="collection-item"><b>{{ lang('config_theme_url') }}</b>: @{{updaterInfo.local.url}}</li>
                        </ul>
                    </div>
                    <div v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version)">
                        <p class="subtitle">{{ lang('config_available_version') }}</p>
                        <ul class="collection">
                            <li class="collection-item"><b>{{ lang('name') }}</b>: @{{updaterInfo.remote.name}}</li>
                            <li class="collection-item"><b>{{ lang('description') }}</b>: @{{updaterInfo.remote.description}}</li>
                            <li class="collection-item"><b>{{ lang('config_theme_version') }}</b>: @{{updaterInfo.remote.version}}</li>
                            <li class="collection-item"><b>{{ lang('config_theme_updated') }}</b>: @{{updaterInfo.remote.updated}}</li>
                            <li class="collection-item"><b>{{ lang('config_theme_url') }}</b>: @{{updaterInfo.remote.url}}</li>
                        </ul>
                    </div>
                    <div v-if="updaterInfo && (updaterInfo.remote.version <= updaterInfo.local.version)">
                        <p>{{ lang('config_up_to_date') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 center" v-show="updaterloader">
                    {{ lang('config_checking_updates') }}
                    <preloader />
                </div>
                <div class="col s12" v-if="!updaterloader && !updaterInfo">
                    <a class="waves-effect waves-light btn btn-accent" @click="checkUpdates()">
                        <i class="material-icons left">sync</i> {{ lang('config_check_updates') }}
                    </a>
                </div>
                <div class="col s12"
                    v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version) && !updaterPackageDownloaded">
                    <div class="center-align" v-if="updaterProgress">
                        {{ lang('config_downloading') }}
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <p v-if="!updaterProgress">
                        <a class="waves-effect waves-light btn btn-accent" @click="downloadUpdateVersion()">
                            <i class="material-icons left">file_download</i> {{ lang('config_download_package') }}
                        </a>
                    </p>
                </div>
                <div class="col s12"
                    v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version) && updaterPackageDownloaded">
                    <div class="center-align" v-if="updaterInstallProgress">
                        {{ lang('config_installing') }}
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <p v-if="!updaterInstallProgress">
                        <a class="waves-effect waves-light btn btn-accent" @click="installDownloadedPackage()">
                            <i class="material-icons left">system_update_alt</i> {{ lang('config_install_package') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
