<script type="text/x-template" id="file-explorar-selector">
<div class="files files-selector">
    <h5>Seleccionar Folder</h5>
    <div class="row search">
        <div class="col s12">
            <nav v-if="!backto" class="search-nav">
                <div class="nav-wrapper">
                    <form>
                        <div class="input-field">
                            <input id="search" type="search" placeholder="Buscar Archivos..." v-model="search" v-on:keyup.enter="searchfiles()">
                            <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                            <i class="material-icons" @click="resetSearch()">close</i>
                        </div>
                    </form>
                    <ul class="right hide-on-med-and-down">
                        <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                        <li><a href="#!" v-on:click="navigateFiles('./')"><i class="material-icons">refresh</i></a></li>
                    </ul>
                </div>
            </nav>
            <nav v-if="backto" class="navigation-nav">
                <div class="nav-wrapper">
                    <a href="#!" class="brand-logo" @click="navigateFiles(getBackPath)"><i class="material-icons">arrow_back</i></a>
                    <div class="col s12 breadcrumb-nav" v-if="getbreadcrumb">
                        <a href="#!" class="breadcrumb" v-for="(item, index) in getbreadcrumb" :key="index" @click="navigateFiles(item.path + item.folder + '/')">@{{item.folder}}</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <div v-if="getFolders.length">
        <div class="col s6 m6 l4 xl3 folder" v-if="curDir == './'">
            <label class="checkbox">
                <input type="checkbox" v-model="selectedRoot"/>
                <span>&nbsp;</span>
            </label>
            <div class="card-panel" @click="navigateFiles('./')">
                <div class="card-icon">
                    <div class="icon">
                        <i class="material-icons">folder</i>
                    </div>
                </div>
                <div class="card-content">
                    <span>
                        root
                    </span>
                </div>
            </div>
        </div>
        <div class="col s12" v-if="!selectedRoot">
            <h5>Folders</h5>
        </div>
        <div class="col s6 m6 l4 xl3 folder" v-if="!selectedRoot" v-for="(item, index) in getFolders" :key="index" >
            <label class="checkbox" >
                <input type="checkbox" v-model="item.selected"/>
                <span>&nbsp;</span>
            </label>
            <div class="card-panel" @click="navigateFiles(item.file_path + item.file_name + '/')">
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
    <div v-if="getFiles.length && ( mode == 'files' || mode == 'all' )">
        <div class="col s12">
            <h5>Files</h5>
        </div>
        <div class="col s12">
            <div class="row">
                <div class="col s12 m6 l4 xl3 file" v-for="(item, index) in getFiles" :key="index">
                    <div class="card">
                        <div class="card-image waves-effect waves-block waves-light"
                            v-if="!isImage(item)">
                            <div class="file icon activator">
                                <i :class="getIcon(item)"></i>
                            </div>
                        </div>
                        <div class="card-image" v-if="isImage(item)">
                            <a :href="getImagePath(item)" data-lightbox="roadtrip"><img
                                    :src="getImagePath(item)"></a>
                        </div>
                        <div class="card-content" @click="setSideRightBarSelectedFile(item);">
                            @{{(item.file_name + getExtention(item)) | shortName}}
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
    <button class="btn waves-effect waves-light" type="submit" v-on:click="onClickButton()" name="action">Seleccionar
        <i class="material-icons right">send</i>
    </button>
</div>
</script>