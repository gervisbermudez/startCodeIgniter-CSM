@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/lightbox2/dist/css/lightbox.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/file_explorer.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/font-awesome/css/all.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/prism/prism.css')?>">
@endsection

@section('content')
<div class="container explorer" id="root">
    <div class="row">
        <div class="col s12">
            <div class="row">
                <div class="col s12 center" v-bind:class="{ hide: !loader }">
                    <preloader />
                </div>
                <div class="row" v-cloak v-show="!loader">
                    <div class="col s12 m2 tree">
                        <ul class="sidenav hide-on-small-only">
                            <li class="uploadbtn">
                                <a class="waves-effect waves-teal btn btn-default modal-trigger" href="#uploaderModal">
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
                        <ul class="collapsible hide-on-med-and-up">
                            <li>
                                <div class="collapsible-header"><a class="subheader"><i class="fas fa-cloud"></i> My
                                        Drive</a></div>
                                <div class="collapsible-body">
                                    <ul class="suboptions">
                                        <li><a href="#" class="waves-effect waves-teal">
                                                <i class="fas fa-file-upload"></i> Add File</a>
                                        </li>
                                        <li><a href="#!" class="waves-effect waves-teal" @click="navigateFiles('./')"><i
                                                    class="far fa-folder left"></i>
                                                All
                                                Files</a></li>
                                        <li>
                                            <a href="#!" class="waves-effect waves-teal"><i class="fas fa-history"></i>
                                                Recents</a>
                                        </li>
                                        <li><a class="waves-effect waves-teal" href="#!"
                                                @click="filterFiles('important')"><i class="fas fa-star"></i>
                                                Important</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!"
                                                @click="filterFiles('trash')"><i class="fas fa-trash"></i> Deleted
                                                Files</a></li>
                                        <li><a class="subheader">Labels</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!"
                                                @click="filterFiles('images')"><i class="far fa-images"></i>
                                                Images</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('docs')"><i
                                                    class="far fa-file-word"></i> Document</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!"
                                                @click="filterFiles('audio')"><i class="far fa-file-audio"></i>
                                                Audios</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!"
                                                @click="filterFiles('video')"><i class="fas fa-file-video"></i>
                                                Videos</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('zip')"><i
                                                    class="fas fa-file-archive"></i> Zip Files</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col s12 m10 files">
                        <div class="file-overlay" v-bind:class="{ show: showSideRightBar }">
                            <i class="material-icons" @click="setCloseSideRightBar();">close</i>
                            <div class="container">
                                <div class="row">
                                    <div class="col s12">
                                        <div v-if="showFile.file_content">
                                            <h4>@{{showFile.file_name + '.' + showFile.file_type }}</h4>
                                            <div v-if="!showFile.isImagen">
                                                <pre :class="'language-' + showFile.file_type">
                                                <code :class="'language-' + showFile.file_type" v-html="showFile.file_content"></code>
                                                </pre>
                                            </div>
                                            <div v-else>

                                                <a :href="getImagePath(showFile)" data-lightbox="roadtrip">
                                                    <img :src="getFullFilePath(showFile)">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="preloader">
                                            <preloader v-show="!showFile.file_content" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row search">
                            <div class="col s12">
                                <nav v-if="!backto" class="search-nav">
                                    <div class="nav-wrapper">
                                        <div class="input-field">
                                            <input class="input-search" type="search" placeholder="Buscar Archivos..."
                                                v-model="search" v-on:keyup="searchfiles()">
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
                                        <div class="col s12 breadcrumb-nav" v-if="getbreadcrumb">
                                            <a href="#!" class="breadcrumb" @click="navigateFiles(getBackPath)"><i
                                                    class="material-icons">arrow_back</i></a>
                                        </div>
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
                                <preloader />
                            </div>
                            <div v-bind:class="{ hide: fileloader }" v-if="recentlyFiles.length">
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
                            <div v-bind:class="{ hide: fileloader }" v-if="getFolders.length">
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
                            <div v-if="getFiles.length" v-bind:class="{ hide: fileloader }">
                                <div class="col s12">
                                    <h5>Files</h5>
                                </div>
                                <div class="col s12">
                                    <div class="row">
                                        <div class="col s12 m6 l4 xl3 file" v-for="(item, index) in getFiles"
                                            :key="index">
                                            <div class="card">
                                                <!-- Dropdown Structure -->
                                                <a class="grey-text text-darken-4 dropdown-trigger" href='#!'
                                                    :data-target="'file_options' + index"><i
                                                        class="material-icons right">more_vert</i></a>
                                                <ul :id="'file_options' + index" class='dropdown-content'
                                                    v-if="curDir != './trash/'">
                                                    <li><a href="#!" @click="getFileContent(item)">
                                                            <i class="fas fa-share-alt"></i> view file</a>
                                                    </li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger" href="#modal1"
                                                            @click="renameFile(item);">
                                                            <i class="far fa-edit"></i>
                                                            Rename</a>
                                                    </li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            @click="setFileToMove(item);" href="#folderSelectorMove">
                                                            <i class="fas fa-cut"></i>
                                                            Move</a>
                                                    </li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            @click="setFileToMove(item);" href="#folderSelectorCopy">
                                                            <i class="fas fa-copy"></i>
                                                            Copy</a>
                                                    </li>
                                                    <li><a href="#!" @click="featuredFileServe(item);">
                                                            <i class="fa-star"
                                                                :class='[item.featured == 1 ? "fas" : "far"]'></i>
                                                            Important</a></li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            href="#deleteFileModal" @click="trashFile(item);">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul :id="'file_options' + index" class='dropdown-content' v-else>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            @click="setFileToMove(item);" href="#folderSelectorMove">
                                                            <i class="fas fa-cut"></i>
                                                            Move</a>
                                                    </li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            href="#deleteFileModal" @click="trashFile(item);">
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
                                                <div class="card-content" @click="setSideRightBarSelectedFile(item);">
                                                    @{{(item.file_name + getExtention(item)) | shortName}}
                                                </div>
                                                <div class="card-reveal">
                                                    <span
                                                        class="card-title grey-text text-darken-4">@{{item.file_name}}<i
                                                            class="material-icons right">close</i></span>
                                                    <p>
                                                        <b>Type</b>: @{{item.file_type}} <br />
                                                        <b>Create</b>: @{{item.date_create}} <br />
                                                        <b>Update</b>: @{{item.date_update}} <br />
                                                        <b>shared</b> with: @{{item.shared_user_group_id}} <br />
                                                        <b>User</b>: @{{item.user_id}} <br />
                                                        <a :href="getFullFilePath(item)" target="_blank">Open File</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getFiles.length == 0 && !fileloader" v-bind:class="{ hide: fileloader }">
                                <div class="row">
                                    <div class="col s12">
                                        <h5><?php echo lang('no_files'); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="file-info-colum side-right" v-bind:class="{ show: showSideRightBar }"
                            v-if="showSideRightBar">
                            <div class="side-header">
                                <span
                                    class="filename">@{{(sideRightBarSelectedFile.file_name + getExtention(sideRightBarSelectedFile))}}</span>
                                <a class="waves-effect waves-light modal-trigger right" href="#deleteFileModal"
                                    @click="trashFile(sideRightBarSelectedFile);">
                                    <i class="fas fa-trash grey-text text-darken-4 "></i></a>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs" id="filetabs">
                                        <li class="tab col s6"><a class="active" href="#fileinfo"><i
                                                    class="material-icons">info_outline</i> Info</a></li>
                                        <li class="tab col s6"><a href="#filehistory"><i
                                                    class="material-icons">linear_scale</i> History</a></li>
                                    </ul>
                                </div>
                                <div class="tabsbody">
                                    <div id="fileinfo" class="col s12">
                                        <div class="preview">
                                            <div class="card-image" v-if="!isImage(sideRightBarSelectedFile)">
                                                <div class="file icon ">
                                                    <i :class="getIcon(sideRightBarSelectedFile)"></i>
                                                </div>
                                                <div class="divider"></div>
                                            </div>
                                        </div>
                                        <ul class="file_options">
                                            <li>
                                                <a :href="getFullFilePath(sideRightBarSelectedFile)" target="_blank"><i
                                                        class="fas fa-cloud-download-alt"></i> Download File</a>
                                            </li>
                                            <li><a href="#!" @click="shareFile(sideRightBarSelectedFile)">
                                                    <i class="fas fa-share-alt"></i> Share file</a>
                                            </li>
                                            <li>
                                                <a class="waves-effect waves-light modal-trigger" href="#modal1"
                                                    @click="renameFile(sideRightBarSelectedFile);">
                                                    <i class="far fa-edit"></i>
                                                    Rename</a>
                                            </li>
                                            <li><a href="#!" @click="featuredFileServe(sideRightBarSelectedFile);">
                                                    <i class="fa-star"
                                                        :class='[sideRightBarSelectedFile.featured == 1 ? "fas" : "far"]'></i>
                                                    Important</a></li>
                                            <li v-if="curDir != './trash/'">
                                                <a class="waves-effect waves-light modal-trigger"
                                                    href="#deleteFileModal"
                                                    @click="trashFile(sideRightBarSelectedFile);">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </li>
                                        </ul>
                                        <ul class="collection">
                                            <li class="collection-item">Type: <span
                                                    class="secondary-content">@{{sideRightBarSelectedFile.file_type}}</span>
                                            </li>
                                            <li class="collection-item">Create: <span
                                                    class="secondary-content">@{{timeAgo(sideRightBarSelectedFile.date_create)}}</span>
                                            </li>
                                            <li class="collection-item">Update: <span
                                                    class="secondary-content">@{{timeAgo(sideRightBarSelectedFile.date_update)}}</span>
                                            </li>
                                            <li class="collection-item">Folder: <span
                                                    class="secondary-content">@{{(sideRightBarSelectedFile.file_path)}}</span>
                                            </li>
                                            <li class="collection-item">File key: <span
                                                    class="secondary-content">@{{(sideRightBarSelectedFile.rand_key)}}</span>
                                            </li>
                                            <li class="collection-item" v-if="sideRightBarSelectedFile.user_group">
                                                Shared with: <span
                                                    class="secondary-content">@{{sideRightBarSelectedFile.user_group.name}}</span>
                                            </li>
                                            <li class="collection-item" v-if="sideRightBarSelectedFile.user">
                                                Create by: <br />
                                                <user-info :user="sideRightBarSelectedFile.user" />
                                            </li>
                                        </ul>

                                    </div>
                                    <div id="filehistory" class="col s12" style="display: none;">
                                        <ul class="collection filehistory">
                                            <li class="collection-item avatar"
                                                v-for="(history, index) in sideRightBarSelectedFile.history"
                                                :key="index">
                                                <i
                                                    class="material-icons circle teal">@{{getFileHistoryIcon(history.action)}}</i>
                                                <span class="title">@{{history.description}}</span>
                                                <p>
                                                    <a :href="history.user.get_profileurl()">@{{history.user.get_fullname()}}
                                                    </a>
                                                    <br>
                                                    @{{timeAgo(history.date_create)}}
                                                </p>
                                            </li>
                                        </ul>

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
    <div id="deleteFileModal" class="modal">
        <div class="modal-content">
            <h4>Delete File</h4>
            <p v-if="curDir != './trash/'">Move this file to trash?</p>
            <p v-else>Delete this file permanently?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
            <a href="#!" v-if="curDir != './trash/'" @click="moveFileTo(moveToTrash, './trash/');"
                class="modal-close waves-effect waves-green btn-flat">Move</a>
            <a href="#!" v-else @click="deleteFile(moveToTrash);"
                class="modal-close waves-effect waves-green btn-flat red white-text">Delete</a>
        </div>
    </div>
    <!-- Modal Structure -->
    <file-explorer-selector :modal="'folderSelectorMove'" :preselected="[]" :mode="'folders'" :multiple="false"
        v-on:notify="moveCallcack">
    </file-explorer-selector>
    <!-- Modal Structure -->
    <file-explorer-selector :modal="'folderSelectorCopy'" :preselected="[]" :mode="'folders'" :multiple="false"
        v-on:notify="copyCallcack">
    </file-explorer-selector>

</div>
<!-- Modal Structure -->
<div id="uploaderModal" class="modal bottom-sheet">
    <div class="modal-content">
        <h4><?php echo lang('upload_files'); ?></h4>
        <input type="file" id="input-100" name="input-100[]" accept="*" multiple>
    </div>
    <div class="modal-footer">
        <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat"><?php echo lang('cancel'); ?></a>
    </div>
</div>



@include('admin.components.file_explorer_selector_component')

@endsection


@section('footer_includes')
<script src="<?=base_url('resources/components/FileExplorerSelector.js');?>"></script>
<script src="<?=base_url('resources/components/FileExplorerModule.js');?>"></script>
<script src="<?=base_url('public/vendors/lightbox2/dist/js/lightbox.min.js');?>"></script>
<script src="<?=base_url('public/vendors/fileinput/js/fileinput.min.js');?>"></script>
<script src="<?=base_url('public/vendors/fileinput/js/plugins/canvas-to-blob.min.js');?>"></script>
<script src="<?=base_url('public/vendors/fileinput/js/locales/es.js');?>"></script>
<script src="<?=base_url('public/vendors/prism/prism.js');?>"></script>

<script>
$(document).on('ready', function() {
    $("#input-100").fileinput({
        uploadUrl: BASEURL + "admin/files/ajax_upload_file",
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
    }).on('fileuploaded', function(event, previewId, index, fileId) {
        FileExplorerModule.reloadFileExplorer();
        M.toast({
            html: "File Uploaded!"
        });
        M.Modal.getInstance($('#uploaderModal')[0]).close();
    });
});
</script>
@endsection