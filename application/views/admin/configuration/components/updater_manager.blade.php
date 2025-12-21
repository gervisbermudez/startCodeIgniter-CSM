<div v-show="sectionActive == 'updater'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Updater Manager</h4>
                </div>
            </div>
            <div class="row" v-if="getConfig('UPDATER_LAST_CHECK_UPDATE')">
                <div class="col s12">
                    <p>
                        <b>Last check</b>: @{{getConfig('UPDATER_LAST_CHECK_UPDATE').config_value}}
                    </p>
                    <div v-if="updaterInfo">
                        <div class="subtitle">
                            Current Start CMS Version:
                        </div>
                        <ul class="collection">
                            <li class="collection-item"><b>Name</b>: @{{updaterInfo.local.name}}</li>
                            <li class="collection-item"><b>Description</b>: @{{updaterInfo.local.description}}</li>
                            <li class="collection-item"><b>Version</b>: @{{updaterInfo.local.version}}</li>
                            <li class="collection-item"><b>Updated</b>: @{{updaterInfo.local.updated}}</li>
                            <li class="collection-item"><b>Url</b>: @{{updaterInfo.local.url}}</li>
                        </ul>
                        <br />
                    </div>
                    <div v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version)">
                        <div class="subtitle">
                            Available Start CMS Version:
                        </div>
                        <ul class="collection">
                            <li class="collection-item"><b>Name</b>: @{{updaterInfo.remote.name}}</li>
                            <li class="collection-item"><b>Description</b>: @{{updaterInfo.remote.description}}</li>
                            <li class="collection-item"><b>Version</b>: @{{updaterInfo.remote.version}}</li>
                            <li class="collection-item"><b>Updated</b>: @{{updaterInfo.remote.updated}}</li>
                            <li class="collection-item"><b>Url</b>: @{{updaterInfo.remote.url}}</li>
                        </ul>
                        <br />
                    </div>
                    <div v-if="updaterInfo && (updaterInfo.remote.version <= updaterInfo.local.version)">
                        You have the last version of Start CMS!
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 center" v-bind:class="{ hide: !updaterloader }">
                    Checking Updates...
                    <br><br>
                    <preloader />
                </div>
                <div class="col s12" v-if="!updaterloader && !updaterInfo">
                    <p>
                        <a class="waves-effect waves-light btn" @click="checkUpdates()"><i
                                class="material-icons left">sync</i> Check for updates</a>
                    </p>
                </div>
                <div class="col s12"
                    v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version) && !updaterPackageDownloaded">
                    <div class="download-progress center-align" v-if="updaterProgress">
                        Downloading package...
                        <br />
                        <br />
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <p v-if="!updaterProgress">
                        <a class="waves-effect waves-light btn" @click="downloadUpdateVersion()"><i
                                class="material-icons left">file_download</i> Download Package</a>
                    </p>
                </div>
                <div class="col s12"
                    v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version) && updaterPackageDownloaded">
                    <div class="download-progress center-align" v-if="updaterInstallProgress">
                        Installing package in progress...
                        <br />
                        <br />
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <p v-if="!updaterInstallProgress">
                        <a class="waves-effect waves-light btn" @click="installDownloadedPackage()"><i
                                class="material-icons left">system_update_alt</i> Install Package</a>
                    </p>
                </div>
            </div>
        </div>
