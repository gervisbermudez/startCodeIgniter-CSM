@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('head_includes')
<!-- <link rel="stylesheet" href="<?= base_url('public/js/fileinput-master/css/fileinput.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('public/js/lightbox2-master/dist/css/lightbox.min.css') ?>"> -->
<link rel="stylesheet" href="<?= base_url('public/css/admin/file_explorer.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('public\font-awesome\css\all.min.css') ?>">
@endsection

@section('content')
<div class="container explorer">
    <div class="row">
        <div class="col s12">
            <div class="row" id="root">
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
                            <li><a class="waves-effect" href="#!"><i class="fas fa-star"></i> Important</a></li>
                            <li><a class="waves-effect" href="#!"><i class="fas fa-trash"></i> Deleted Files</a></li>
                            <li><a class="subheader">Labels</a></li>
                            <li><a class="waves-effect" href="#!"><i class="far fa-images"></i> Images</a></li>
                            <li><a class="waves-effect" href="#!"><i class="far fa-file-word"></i> Document</a></li>
                            <li><a class="waves-effect" href="#!"><i class="far fa-file-audio"></i> Audios</a></li>
                            <li><a class="waves-effect" href="#!"><i class="fas fa-file-video"></i> Videos</a></li>
                            <li><a class="waves-effect" href="#!"><i class="fas fa-file-archive"></i> Zip Files</a></li>


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
                                                    required>
                                                <label class="label-icon" for="search"><i
                                                        class="material-icons">search</i></label>
                                                <i class="material-icons">close</i>
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
                            <div class="col s12">
                                <h5 v-if="getFiles.length">Files</h5>
                                <div class="row">
                                    <div class="col s3" v-for="(item, index) in getFiles" :key="index">
                                        <div class="card file">
                                            <div class="card-image waves-effect waves-block waves-light">
                                                <div class="file icon">
                                                    <i :class="getIcon(item)"></i>
                                                </div>
                                                <span class="activator grey-text text-darken-4"><i
                                                        class="material-icons right">more_vert</i></span>
                                            </div>
                                            <div class="card-content">
                                                @{{(item.file_name + getExtention(item)) | shortName}}
                                                <p><a href="#">This is a link</a></p>
                                            </div>
                                            <div class="card-reveal">
                                                <span class="card-title grey-text text-darken-4">@{{item.file_name}}<i
                                                        class="material-icons right">close</i></span>
                                                <p>Here is some more information about this product that is only
                                                    revealed once
                                                    clicked on.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="<?= base_url('public/js/components/fileExplorerModule.min.js') ?>"></script>
@endsection