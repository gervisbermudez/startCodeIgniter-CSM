<!-- Modal Structure -->
<div id="fileUploader" class="modal modal-fixed-footer fileSelector">
    <div class="modal-content">
        <h4>Cambiar Foto de Perfil</h4>
        <div class="row">
            <div class="col s12">
                <ul class="tabs">
                    <li class="tab col s3"><a class="active" href="#test1"><?php echo lang('upload_file'); ?></a></li>
                    <li class="tab col s3"><a href="#test2"  @click="navigateFiles('./')"><?php echo lang('your_files'); ?></a></li>
                    <li class="tab col s3"><a href="#test2" @click="filterFiles('images')">Imagenes</a></li>
                </ul>
            </div>
            <div id="test1" class="col s12">
                <input type="file" id="input-100" name="input-100[]" accept="image/x-png,image/gif,image/jpeg">
            </div>
            <div class="explorer">
                <div id="test2" class="col s12">
                    <div class="col s12 files">
                        <div class="row search">
                            <div class="col s12">
                                <nav v-if="!backto" class="search-nav">
                                    <div class="nav-wrapper">
                                        <div class="input-field">
                                            <input class="input-search" type="search" placeholder="Buscar Archivos..."
                                                v-model="search" v-on:keyup.enter="searchfiles()">
                                            <label class="label-icon" for="search"><i
                                                    class="material-icons">search</i></label>
                                            <i class="material-icons" @click="resetSearch()">close</i>
                                        </div>
                                        <ul class="right hide-on-med-and-down">
                                            <li><a href="#!" v-on:click="toggleView();"><i
                                                        class="material-icons">view_module</i></a></li>
                                            <li><a href="#!" v-on:click="reloadFileExplorer();"><i
                                                        class="material-icons">refresh</i></a></li>
                                        </ul>
                                    </div>
                                </nav>
                                <nav v-if="backto" class="navigation-nav">
                                    <div class="nav-wrapper">
                                        <a href="#!" class="brand-logo" @click="navigateFiles(getBackPath)"><i
                                                class="material-icons">arrow_back</i></a>
                                        <div class="col s12 breadcrumb-nav" v-if="getbreadcrumb">
                                            <a href="#!" class="breadcrumb" v-for="(item, index) in getbreadcrumb"
                                                :key="index"
                                                @click="navigateFiles(item.path + item.folder + '/')">@{{item.folder}}</a>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                        <div class="row filelist">
                            <div class="col s12 center" v-bind:class="{ hide: !fileloader }">
                                <div class="progress">
                                    <div class="indeterminate"></div>
                                </div>
                            </div>
                            <div v-if="recentlyFiles.length">
                                <div class="col s12">
                                    <h5>Recently Accessed Files</h5>
                                </div>
                                <div class="col s12 m6 l4 xl3" v-for="(item, index) in recentlyFiles" :key="index">
                                    <div class="card">
                                        <div class="card-image">
                                            <div class="icon">
                                                <i class="material-icons">@{{getIcon(item)}}</i>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <p>
                                                @{{item.file_name}}@{{getExtention(item)}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getFolders.length">
                                <div class="col s12">
                                    <h5>Folders</h5>
                                </div>
                                <div class="col s12 m6 l4 xl3 folder" v-for="(item, index) in getFolders" :key="index"
                                    @click="navigateFiles(item.file_path + item.file_name + '/')">
                                    <div class="card-panel">
                                        <div class="card-icon">
                                            <div class="icon">
                                                <i class="material-icons">folder</i>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <span>
                                                @{{item.file_name}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getFiles.length">
                                <div class="col s12">
                                    <h5>Files</h5>
                                </div>
                                <div class="col s12">
                                    <div class="row">
                                        <div class="col s12 m6 l4 xl3 file" v-for="(item, index) in getFiles" :key="index">
                                            <div class="card file" v-on:click="setSelectedFile(item);">
                                                    <label>
                                                      <input type="checkbox" :checked="!!item.isSelected" v-model="item.isSelected" >
                                                      <span></span>
                                                    </label>
                                                <div class="card-image waves-effect waves-block waves-light"
                                                    v-if="!isImage(item)">
                                                    <div class="file icon text-center">
                                                        <i :class="getIcon(item)"></i>
                                                    </div>
                                                </div>
                                                <div class="card-image" v-if="isImage(item)" :style="{background: 'url('+ getImagePath(item)+ ')'}">
                                                </div>
                                                <div class="card-content">
                                                    @{{(item.file_name + getExtention(item)) | shortName}}                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getFiles.length == 0 && !fileloader">
                                <div class="row">
                                    <div class="col s12">
                                        <h5><?php echo lang('no_files'); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button v-if="doneSelection" @click="finishedSelection();" class="modal-close btn waves-effect waves-light" type="submit" name="action">Seleccionar Imagen
            <i class="material-icons right">send</i>
          </button>
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cerrar</a>
    </div>
</div>