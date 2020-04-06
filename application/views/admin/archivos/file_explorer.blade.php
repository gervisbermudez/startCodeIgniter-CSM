@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('head_includes')
<link rel="stylesheet" href="<?= base_url('public/js/lightbox2-master/dist/css/lightbox.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('public/css/admin/file_explorer.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('public\font-awesome\css\all.min.css') ?>">
@endsection

@section('content')
<div class="container explorer" id="root">
    <div class="row">
        <div class="col s12">
            <div class="row">
                <div class="col s12 center" v-bind:class="{ hide: !loader }">
                    <div class="preloader-wrapper big active">
                        <div class="spinner-layer spinner-blue-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div>
                            <div class="gap-patch">
                                <div class="circle"></div>
                            </div>
                            <div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" v-cloak v-if="!loader">
                    <div class="col s2 tree">
                        <ul class="sidenav">
                            <li><a href="#" class="waves-effect waves-teal btn btn-default">
                                    <i class="material-icons left">file_upload</i> Add File</a>
                            </li>
                            <li><a class="subheader">My Drive</a></li>
                            <li><a href="#!" @click="navigateFiles('./')"><i class="far fa-folder left"></i> All
                                    Files</a></li>
                            <li>
                                <a href="#!"><i class="fas fa-history"></i> Recents</a>
                            </li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('important')"><i
                                        class="fas fa-star"></i> Important</a></li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('trash')"><i
                                        class="fas fa-trash"></i> Deleted Files</a></li>
                            <li><a class="subheader">Labels</a></li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('images')"><i
                                        class="far fa-images"></i>
                                    Images</a></li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('docs')"><i
                                        class="far fa-file-word"></i> Document</a></li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('audio')"><i
                                        class="far fa-file-audio"></i> Audios</a></li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('video')"><i
                                        class="fas fa-file-video"></i> Videos</a></li>
                            <li><a class="waves-effect" href="#!" @click="filterFiles('zip')"><i
                                        class="fas fa-file-archive"></i> Zip Files</a></li>


                        </ul>
                    </div>
                    <div class="col s10 files">
                        <div class="row search">
                            <div class="col s12">
                                <nav v-if="!backto" class="search-nav">
                                    <div class="nav-wrapper">
                                        <form>
                                            <div class="input-field">
                                                <input id="search" type="search" placeholder="Buscar Archivos..."
                                                    v-model="search" v-on:keyup.enter="searchfiles()">
                                                <label class="label-icon" for="search"><i
                                                        class="material-icons">search</i></label>
                                                <i class="material-icons" @click="resetSearch()">close</i>
                                            </div>
                                        </form>
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
                            <div class="col s12" v-if="recentlyFiles.length">
                                <h5>Recently Accessed Files</h5>
                            </div>
                            <div class="col s3" v-for="(item, index) in recentlyFiles" :key="index">
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
                            <div class="col s12" v-if="getFolders.length">
                                <h5>Folders</h5>
                            </div>
                            <div class="col s3 folder" v-for="(item, index) in getFolders" :key="index"
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
                            <div class="col s12" v-if="getFiles.length">
                                <h5>Files</h5>
                                <div class="row">
                                    <div class="col s4 file" v-for="(item, index) in getFiles" :key="index">
                                        <div class="card">
                                            <!-- Dropdown Structure -->
                                            <a class="grey-text text-darken-4 dropdown-trigger" href='#!'
                                                :data-target="'file_options' + index"><i
                                                    class="material-icons right">more_vert</i></a>
                                            <ul :id="'file_options' + index" class='dropdown-content'>
                                                <li><a href="#!">
                                                    <i class="fas fa-share-alt"></i> Share file</a>
                                                </li>
                                                <li>
                                                    <a class="waves-effect waves-light modal-trigger" href="#modal1"
                                                        @click="renameFile(item);">
                                                        <i class="far fa-edit"></i>
                                                        Rename</a>
                                                </li>
                                                <li><a href="#!" @click="featuredFileServe(item);">
                                                    <i class="fa-star" :class='[item.featured == 1 ? "fas" : "far"]'></i> Important</a></li>
                                                <li v-if="curDir != './trash/'">
                                                    <a href="#!" @click="moveFileTo(item, './trash/');">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
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
                                            <div class="card-content">
                                                @{{(item.file_name + getExtention(item)) | shortName}}
                                                <p><a href="#">Share file</a></p>
                                            </div>
                                            <div class="card-reveal">
                                                <span class="card-title grey-text text-darken-4">@{{item.file_name}}<i
                                                        class="material-icons right">close</i></span>
                                                <p>
                                                    <b>Type</b>: @{{item.file_type}} <br />
                                                    <b>Create</b>: @{{item.date_create}} <br />
                                                    <b>Update</b>: @{{item.date_update}} <br />
                                                    <b>shared</b> with: @{{item.shared_user_group_id}} <br />
                                                    <b>User</b>: @{{item.user_id}} <br />
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col s12">
                                        <h5>No hay archivos</h5>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Structure -->
    <div id="modal1" class="modal">
        <div class="modal-content">
            <h4>Rename File</h4>
            <p>
                <div class="input-field col s6">
                    <input placeholder="Placeholder" id="first_name" type="text" v-on:keyup.enter="renameFileServe()"
                        v-model="editFile.new_name" class="validate">
                    <label for="first_name">Rename</label>
                </div>
            </p>
        </div>
        <div class="modal-footer">
            <a href="#!" @click="renameFileServe()" class="modal-close waves-effect waves-green btn-flat">Agree</a>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="<?= base_url('public/js/components/fileExplorerModule.min.js') ?>"></script>
<script src="<?= base_url('public\js\lightbox2-master\dist\js\lightbox.min.js') ?>"></script>
@endsection