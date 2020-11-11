<script type="text/x-template" id="file-explorar-selector">
<div :id="modal" class="modal modal-fixed-footer file-explorar-selector">
    <div class="modal-content">
    <div class="row">
    <div class="col s12">
      <ul class="tabs" id="selectorTabs">
        <li class="tab col s3"><a  class="active" href="#selector" @click="destroyFileinputInstance();"><i class="material-icons">folder_open</i> My Files</a></li>
        <li class="tab col s3"><a href="#uploader" @click="initUploader();"  v-if="uploader"><i class="material-icons">cloud_upload</i> Upload</a></li>
      </ul>
    </div>
    <div id="selector" class="col s12">
        <h4><i class="material-icons left">content_copy</i> @{{title}}</h4>
        <div class="files files-selector">
            <div class="row search">
                <div class="col s12">
                    <nav v-if="!backto" class="search-nav">
                        <div class="nav-wrapper">
                            <div class="input-field">
                                <input class="input-search" type="search" placeholder="Buscar..."
                                    v-model="search" v-on:keyup.enter="searchfiles()" />
                                <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                <i class="material-icons" @click="resetSearch()">close</i>
                            </div>
                            <ul class="right hide-on-med-and-down">
                                <li>
                                    <a href="#!" v-on:click="toggleView();"><i
                                            class="material-icons">view_module</i></a>
                                </li>
                                <li>
                                    <a href="#!" v-on:click="updateSelector()"><i
                                            class="material-icons">refresh</i></a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <nav v-if="backto" class="navigation-nav">
                        <div class="nav-wrapper">
                            <a href="#!" class="brand-logo" @click="navigateFiles(getBackPath)"><i
                                    class="material-icons">arrow_back</i></a>
                            <div class="col s12 breadcrumb-nav" v-if="getbreadcrumb">
                                <a href="#!" class="breadcrumb" v-for="(item, index) in getbreadcrumb" :key="index"
                                    @click="navigateFiles(item.path + item.folder + '/')">@{{ item.folder }}</a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="row selector-content" >
                <div v-if="getFolders.length && ( mode == 'folders' || mode == 'all' )"">
                    <div class="col s6 m6 l4 xl3 folder" v-if="curDir == './'">
                        <label class="checkbox">
                            <input type="checkbox" v-model="selectedRoot" />
                            <span>&nbsp;</span>
                        </label>
                        <div class="card-panel" @click="navigateFiles('./')">
                            <div class="card-icon">
                                <div class="icon">
                                    <i class="material-icons">folder</i>
                                </div>
                            </div>
                            <div class="card-content">
                                <span> root </span>
                            </div>
                        </div>
                    </div>
                    <div class="col s12" v-if="!selectedRoot">
                        <h5>Folders</h5>
                    </div>
                    <div class="col s6 m6 l4 xl3 folder" v-if="!selectedRoot" v-for="(item, index) in getFolders"
                        :key="index">
                        <label class="checkbox">
                            <input type="checkbox" v-model="item.selected" v-if="showCheckbox(item)"/>
                            <span>&nbsp;</span>
                        </label>
                        <div class="card-panel" @click="navigateFiles(item.file_path + item.file_name + '/')">
                            <div class="card-icon">
                                <div class="icon">
                                    <i class="material-icons">folder</i>
                                </div>
                            </div>
                            <div class="card-content">
                                <span> @{{ item.file_name }} </span>
                            </div>
                        </div>
                    </div>
                    <div class="col s6 m6 l4 xl3 folder new-folder" v-if="create_folder_process">
                        <div class="card-panel">
                            <div class="card-icon">
                                <div class="icon">
                                    <i class="material-icons">folder</i>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="input-field">
                                    <input type="text" id="folder_name" class="validate" value="New Folder" v-on:blur="makeFolderServer();" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="getFiles.length && ( mode == 'files' || mode == 'all' )">
                    <div class="col s12">
                        <h5>Files</h5>
                    </div>
                    <div class="col s12">
                        <div class="row">
                            <div class="col s12 m6 l4 xl3 file" v-for="(item, index) in getFiles" :key="index">
                                <div class="card">
                                    <label class="checkbox">
                                        <input type="checkbox" v-model="item.selected" v-if="showCheckbox(item)"/>
                                        <span>&nbsp;</span>
                                    </label>
                                    <div class="card-image waves-effect waves-block waves-light" v-if="!isImage(item)">
                                        <div class="file icon activator">
                                            <i :class="getIcon(item)"></i>
                                        </div>
                                    </div>
                                    <div class="card-image" v-if="isImage(item)">
                                        <img class="materialboxed" :src="getImagePath(item)" />
                                    </div>
                                    <div class="card-content" @click="setSideRightBarSelectedFile(item);">
                                        @{{ item.file_name + getExtention(item) | shortName }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="getFiles.length == 0 && !fileloader && ( mode == 'files' || mode == 'all' )">
                    <div class="row">
                        <div class="col s12">
                            <h5>No hay archivos</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="uploader" class="col s12"  v-if="uploader">
        <h4><i class="material-icons">file_upload</i> Upload Files</h4>
        <p>
            Current dir: @{{curDir}}
        </p>
        <input type="file" id="input-100" name="input-100[]" accept="*" multiple>
    </div>
  </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
        <a href="#!" class="modal-action waves-effect waves-green btn" v-if="(mode != 'files')" @click="makeNewFolder()"><i class="material-icons left">create_new_folder</i> New Folder</a>
        <button class="btn waves-effect waves-light" type="submit" v-on:click="onClickButton()" name="action">
            Seleccionar
            <i class="material-icons right">send</i>
        </button>
    </div>
</div>
</script>
