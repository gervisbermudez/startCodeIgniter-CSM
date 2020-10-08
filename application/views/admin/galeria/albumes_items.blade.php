@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/file_explorer.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/header.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/js/lightbox2-master/dist/css/lightbox.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/js/fileinput-master/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>">
@endsection

@section('content')
<div class="container" id="root">
    <div class="header" v-cloak>
        <div class="row" v-show="!loader && album.items.length > 0">
            <div class="col s12">
                <span class="page-header">@{{album.name}}</span>
                <a class='dropdown-trigger right' href='#!' :data-target='"album" + album.album_id'><i
                        class="material-icons">more_vert</i></a>
                <ul :id='"album" + album.album_id' class='dropdown-content'>
                    <li><a :href="base_url('admin/paginas/editar/' + album.album_id)">Editar</a></li>
                    <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                    <li v-if="album.status == 2"><a :href="base_url('admin/paginas/preview?album_id=' + album.album_id)"
                            target="_blank">Preview</a></li>
                    <li><a :href="base_url(album.path)" target="_blank">Archivar</a></li>
                </ul>
            </div>
            <div class="col s12">
                <span class="page-subheader">@{{getcontentText(album.description)}}</span>
            </div>
            <div class="col s12">
                <div class="author">
                    Create by: <br />
                </div>
                <user-info v-if="album.user.user_id" :user="album.user" />
            </div>
            <div class="col s12">
                <ul>
                    <li><b>Fecha de publicacion:</b> @{{album.date_publish ? timeAgo(album.date_publish) : timeAgo(album.date_create)}}</li>
                    <li><b>Estado:</b>
                        <span v-if="album.status == 1">
                            Publicado
                        </span>
                        <span v-else>
                            Borrador
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
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
    <nav class="page-navbar" v-cloak v-show="!loader && album.items.length > 0">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                    <input class="input-search" type="search" placeholder="Buscar..." v-model="filter">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                <li><a href="#!" v-on:click="getPages();"><i class="material-icons">refresh</i></a></li>
                <li>
                    <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i
                            class="material-icons">more_vert</i></a>
                    <!-- Dropdown Structure -->
                    <ul id='dropdown-options' class='dropdown-content'>
                        <li><a href="#!">one</a></li>
                        <li><a href="#!">two</a></li>
                        <li class="divider" tabindex="-1"></li>
                        <li><a href="#!">three</a></li>
                        <li><a href="#!"><i class="material-icons">view_module</i>four</a></li>
                        <li><a href="#!"><i class="material-icons">cloud</i>five</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="pages" v-cloak v-if="!loader && album.items.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Album Title</th>
                            <th>Description</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(album, index) in filterData" :key="index">
                            <td>@{{album.name}}</td>
                            <td>@{{getcontentText(album.description)}}</td>
                            <td>
                                @{{album.date_publish ? album.date_publish : album.date_create}}
                            </td>
                            <td>
                                <i v-if="album.status == 1" class="material-icons tooltipped" data-position="left"
                                    data-delay="50" data-tooltip="Publicado">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    data-tooltip="Borrador">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + album.album_item_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + album.album_item_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/paginas/editar/' + album.album_item_id)">Editar</a>
                                    </li>
                                    <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                                    <li v-if="album.status == 2"><a
                                            :href="base_url('admin/paginas/preview?album_item_id=' + album.album_item_id)"
                                            target="_blank">Preview</a></li>
                                    <li><a :href="base_url(album.path)" target="_blank">Archivar</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(album, index) in filterData" :key="index">
                <div class="card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img class="materialboxed" :src="getPageImagePath(album)" />
                        </div>
                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown" + album.album_item_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + album.album_item_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/paginas/editar/' + album.album_item_id)">Editar</a></li>
                            <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                            <li v-if="album.status == 2"><a
                                    :href="base_url('admin/paginas/preview?album_item_id=' + album.album_item_id)"
                                    target="_blank">Preview</a></li>
                            <li><a :href="base_url(album.path)" target="_blank">Archivar</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                            <span class="card-title activator">@{{album.name}}
                                <i v-if="album.status == 1" class="material-icons tooltipped" data-position="left"
                                    data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    data-tooltip="Privado">lock</i>
                            </span>
                            <div class="card-info activator">
                                <p>
                                    @{{getcontentText(album.description)}}
                                </p>
                            </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{album.name}}
                        </span>
                        <span class="subtitle">
                            @{{getcontentText(album.description)}}
                        </span>
                        <ul>
                            <li><b>Fecha de publicacion:</b> <br>
                                @{{album.date_publish ? album.date_publish : album.date_create}}</li>
                            <li><b>Estado:</b>
                                <span v-if="album.status == 1">
                                    Publicado
                                </span>
                                <span v-else>
                                    Borrador
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && album.items.length == 0" v-cloak>
        <h4>No hay paginas</h4>
    </div>
    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped modal-trigger"
            @click="setFileToMove(item);" href="#folderSelector" data-position="left" data-delay="50"
            data-tooltip="Agregar Imagenes al Album">
            <i class="large material-icons">add</i>
        </a>
    </div>
    <!-- Modal Structure -->
    <div id="folderSelector" class="modal">
        <div class="modal-content">
            <h4><i class="material-icons left">content_copy</i> Copy file</h4>
            <file-explorer-selector :mode="'files'" filter="'images'" :multiple="true" v-on:notify="copyCallcack">
            </file-explorer-selector>
        </div>
        <div class="modal-footer">
            <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
        </div>
    </div>
</div>
@include('admin.components.FileExplorerSelector')

@endsection

@section('footer_includes')
<script src="<?=base_url('public/js/components/FileExplorerSelector.min.js');?>"></script>
<script src="<?=base_url('public/js/lightbox2-master/dist/js/lightbox.min.js');?>"></script>
<script src="<?=base_url('public/js/fileinput-master/js/fileinput.min.js');?>"></script>
<script src="<?=base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js');?>"></script>
<script src="<?=base_url('public/js/fileinput-master/js/locales/es.js');?>"></script>
<script>
    $(document).on('ready', function () {
        $("#input-100").fileinput({
            uploadUrl: BASEURL + "admin/archivos/ajax_upload_file",
            enableResumableUpload: true,
            resumableUploadOptions: {
                // uncomment below if you wish to test the file for previous partial uploaded chunks
                // to the server and resume uploads from that point afterwards
                // testUrl: "http://localhost/test-upload.php"
            },
            uploadExtraData: {
                'uploadToken': 'SOME-TOKEN', // for access control / security
                'curDir': './uploads'
            },
            showCancel: true,
            initialPreview: [],
            fileActionSettings: {
                showRemove: true,
                showUpload: true,
                showDownload: true,
                showZoom: true,
                showDrag: true,
                removeIcon: '<i class="fas fa-trash"></i>',
                removeClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary',
                removeErrorClass: 'btn btn-sm btn-kv btn-danger',
                removeTitle: 'Remove file',
                uploadIcon: '<i class="fas fa-upload"></i>',
                uploadClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary',
                uploadTitle: 'Upload file',
                uploadRetryIcon: '<i class="glyphicon glyphicon-repeat"></i>',
                uploadRetryTitle: 'Retry upload',
                downloadIcon: '<i class="fas fa-download"></i>',
                downloadClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary',
                downloadTitle: 'Download file',
                zoomIcon: '<i class="fas fa-search-plus"></i>',
                zoomClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary',
                zoomTitle: 'View Details',
                dragIcon: '<i class="fas fa-arrows-alt"></i>',
                dragClass: 'text-info',
                dragTitle: 'Move / Rearrange',
                dragSettings: {},
                indicatorNew: '<i class="glyphicon glyphicon-plus-sign text-warning"></i>',
                indicatorSuccess: '<i class="glyphicon glyphicon-ok-sign text-success"></i>',
                indicatorError: '<i class="glyphicon glyphicon-exclamation-sign text-danger"></i>',
                indicatorLoading: '<i class="glyphicon glyphicon-hourglass text-muted"></i>',
                indicatorPaused: '<i class="glyphicon glyphicon-pause text-primary"></i>',
                indicatorNewTitle: 'Not uploaded yet',
                indicatorSuccessTitle: 'Uploaded',
                indicatorErrorTitle: 'Upload Error',
                indicatorLoadingTitle: 'Uploading ...',
                indicatorPausedTitle: 'Upload Paused'
            },
            uploadIcon: '<i class="fas fa-upload"></i>',
            removeIcon: '<i class="fas fa-trash"></i>',
            overwriteInitial: false,
            // initialPreview: [],          // if you have previously uploaded preview files
            // initialPreviewConfig: [],    // if you have previously uploaded preview files
            deleteUrl: "http://localhost/file-delete.php",
            progressClass: 'determinate progress-bar bg-success progress-bar-success progress-bar-striped active',
            progressInfoClass: 'determinate progress-bar bg-info progress-bar-info progress-bar-striped active',
            progressCompleteClass: 'determinate progress-bar bg-success progress-bar-success',
            progressPauseClass: 'determinate progress-bar bg-primary progress-bar-primary progress-bar-striped active',
            progressErrorClass: 'determinate progress-bar bg-danger progress-bar-danger',
        }).on('fileuploaded', function (event, previewId, index, fileId) {
            fileExplorerModule.reloadFileExplorer();
            M.toast({ html: "File Uploaded!" });
            M.Modal.getInstance($('#uploaderModal')[0]).close();
        });
    });
</script>
<script src="{{base_url('public/js/components/AlbumsItemsLists.min.js')}}"></script>
@endsection