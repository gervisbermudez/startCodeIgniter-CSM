@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?= base_url('public/css/admin/userprofile.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('public/js/fileinput-master/css/fileinput.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('public/font-awesome/css/all.min.css') ?>">
@endsection

@section('content')
<div id="root">
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
        <div class="col s12 m5" v-cloak v-show="!loader">
            <div class="card banner">
                <!-- Dropdown Trigger -->
                <a class='dropdown-trigger right' href='#' data-target='<?= $dropdown_id ?>'>
                    <i class="material-icons">more_vert</i></a>
                <?= $dropdown_menu ?>
                <div class="card-image">
                    <img src="<?= base_url('public/img/profile/usertop.jpg'); ?>">
                </div>
                <div class="avatar">
                    <a href="#fileUploader" class="modal-trigger" v-if="user.user_data.avatar">
                        <img :src="user. get_avatarurl()" :alt="user.username" class="circle z-depth-1">
                    </a>
                    <a href="#fileUploader" class="modal-trigger" v-else>
                        <i class="material-icons circle grey lighten-5 z-depth-1">account_circle</i></a>
                </div>
                <div class="card-content row">
                    <div class="col s12 center-align">
                        <span class="card-title">@{{user.get_fullname()}}</span>
                        <p>
                            @{{user.role}}
                        </p>
                    </div>
                    <div class="col s12 center-align">
                        <span class="card-title">@{{user.username}}</span>
                        <p>
                            @{{timeAgo(user.lastseen)}}
                        </p>
                    </div>
                </div>
            </div>
            <ul class="collection with-header">
                <li class="collection-header">
                    <span>Datos del usuario</span>
                </li>
                <li class="collection-item"><i class="material-icons left">message</i>
                    <a href="#!"><?= $user->email ?></a>
                </li>
                <li class="collection-item"><i class="material-icons left">contact_phone</i>
                    <a href="#!"><?= $user->user_data->telefono ?></a>
                </li>
                <li class="collection-item"><i class="material-icons left">location_on</i>
                    <a href="#!"><?= $user->user_data->direccion ?></a>
                </li>
            </ul>
        </div>
        <div class="col s12 m7 timeline-content" v-cloak v-show="!loader">
            <div class="row">
                <div class="col s12">
                    <ul class="tabs" id="user-tabs">
                        <li class="tab col s12 m4"><a class="active" href="#test1"> <i class="material-icons">view_day</i> Actividad</a></li>
                    </ul>
                </div>
                <div id="test1" class="col s12">
                    <ul class="timeline">
                        <!-- timeline time label -->
                        <template v-for="(item, index) in timelineData">
                            <li class="time-label">
                                <span class="bg-red">
                                    @{{timeAgo(index) | shortDate}}
                                </span>
                            </li>
                            <!-- /.timeline-label -->
                            <!-- timeline item -->
                            <li v-for="(element, i) in item" :key="makeid(5)">
                                <template v-if="element.model_type == 'page'">
                                    <i class="fa fa-envelope bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o" class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="element.date_create"></i> @{{timeAgo(element.date_create)}}</span>
                                        <h3 class="timeline-header"><a href="#">You</a> created a page</h3>
                                        <page-card :page="element"></page-card>
                                    </div>
                                </template>
                                <template v-if="element.model_type == 'form_custom'">
                                    <i class="fa fa-comments bg-yellow"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fa fa-clock-o" class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="element.date_create"></i> @{{timeAgo(element.date_create)}}</span>
                                        <h3 class="timeline-header"><a href="#">You</a> created a custom form</h3>
                                        <form-custom :form="element"></form-custom>
                                    </div>
                                </template>
                            </li>
                        </template>
                        <li>
                            <i class="fa fa-clock-o bg-gray"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/x-template" id="page-card-template">
    <div class="pages">
            <div class="card page-card">
                <div class="card-image">
                    <div class="card-image-container">
                        <img :src="getPageImagePath(page)" />
                    </div>
                    <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                        :data-target='"page_id" + page.page_id'>
                        <i class="material-icons">more_vert</i></a>
                    <ul :id='"page_id" + page.page_id' class='dropdown-content'>
                        <li><a :href="base_url('admin/paginas/editar/' + page.page_id)">Editar</a></li>
                        <li><a href="#!" v-on:click="deletePage(page, index);">Borrar</a></li>
                        <li v-if="page.status == 2"><a :href="base_url('admin/paginas/preview?page_id=' + page.page_id)" target="_blank">Preview</a></li>
                        <li><a :href="base_url(page.path)" target="_blank">Archivar</a></li>
                    </ul>
                </div>
                <div class="card-content">
                    <div>
                        <span class="card-title"><a :href="base_url(page.path)" target="_blank">@{{page.title}}</a> <i v-if="page.visibility == 1" class="material-icons tooltipped"
                                data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                            <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                data-tooltip="Privado">lock</i>
                        </span>
                        <div class="card-info">
                            <p>
                                @{{getcontentText(page)}}
                            </p>
                            <span class="activator right"><i class="material-icons">more_vert</i></span>
                            <ul>
                                <li class="truncate">
                                    Author: <a :href="base_url('admin/usuarios/ver/' + page.user_id)">@{{page.username}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-reveal">
                    <span class="card-title grey-text text-darken-4">
                        <i class="material-icons right">close</i>
                        @{{page.title}}
                    </span>
                    <span class="subtitle">
                        @{{page.subtitle}}
                    </span>
                    <ul>
                        <li><b>Fecha de publicacion:</b> <br> @{{page.date_publish ? page.date_publish : page.date_create}}</li>
                        <li><b>Categorie:</b> @{{page.categorie}}</li>
                        <li><b>Subcategorie:</b> @{{page.subcategorie ? page.subcategorie : 'Ninguna'}}</li>
                        <li><b>Template:</b> @{{page.template}}</li>
                        <li><b>Type:</b> @{{page.page_type_name}}</li>
                        <li><b>Estado:</b>
                            <span v-if="page.status == 1">
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
</script>

<script type="text/x-template" id="form-custom-template">
    <div>
        <div class="timeline-body">
            <div>
                <a class="btn-floating waves-effect waves-light dropdown-trigger right" href='#!'
								:data-target='"form_custom_id" + form.form_custom_id'>
								<i class="material-icons">more_vert</i></a>
                <h3>@{{form.form_name}}</h3>
				<ul :id='"form_custom_id" + form.form_custom_id' class='dropdown-content'>
					<li><a :href="base_url('admin/formularios/addData/' + form.form_custom_id)"> Agregar data</a></li>
					<li><a :href="base_url('admin/formularios/editForm/' + form.form_custom_id)"> Editar</a></li>
					<li><a href="#!" v-on:click="deleteForm(form_custom, index)"> Borrar</a></li>
				</ul>
            </div>
            @{{form.form_description}}
            <br/>
            <br/>
            <ul>
				<li><b>Categorie:</b> @{{form.categorie}}</li>
				<li><b>Subcategorie:</b> @{{form.subcategorie ? form.subcategorie : 'Ninguna'}}</li>
				<li><b>Template:</b> @{{form.template}}</li>
				<li><b>Type:</b> @{{form.page_type_name}}</li>
				<li><b>Estado:</b>
					<span v-if="form.status == 1">
						Publicado
					</span>
					<span v-else>
						Borrador
					</span>
				</li>
			</ul>
        </div>
        <div class="timeline-footer">
            <a class="btn btn-warning btn-xs" :href="base_url('admin/formularios/addData/' + form.form_custom_id)"><i class="material-icons left">add</i> Add content</a>
        </div>
    </div>
</script>

@include('admin.components.fileUploaderComponent')
@endsection

@section('footer_includes')
<script src="<?= base_url('public/js/components/fileUploaderModule.min.js'); ?>"></script>
<script src="<?= base_url('public/js/components/userProfileComponent.min.js'); ?>"></script>
<script src="<?= base_url('public/js/fileinput-master/js/fileinput.min.js'); ?>"></script>
<script src="<?= base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js'); ?>"></script>
<script src="<?= base_url('public/js/fileinput-master/js/locales/es.js'); ?>"></script>
<script>
    $(document).on('ready', function() {
        $("#input-100").fileinput({
            uploadUrl: BASEURL + "admin/archivos/ajax_upload_file"
            , enableResumableUpload: true
            , resumableUploadOptions: {
                // uncomment below if you wish to test the file for previous partial uploaded chunks
                // to the server and resume uploads from that point afterwards
                // testUrl: "http://localhost/test-upload.php"
            }
            , uploadExtraData: {
                'uploadToken': 'SOME-TOKEN'
                , 'curDir': './uploads'
            }
            , showCancel: true
            , initialPreview: []
            , fileActionSettings: {
                showRemove: true
                , showUpload: true
                , showDownload: true
                , showZoom: true
                , showDrag: true
                , removeIcon: '<i class="fas fa-trash"></i>'
                , removeClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary'
                , removeErrorClass: 'btn btn-sm btn-kv btn-danger'
                , removeTitle: 'Remove file'
                , uploadIcon: '<i class="fas fa-upload"></i>'
                , uploadClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary'
                , uploadTitle: 'Upload file'
                , uploadRetryIcon: '<i class="glyphicon glyphicon-repeat"></i>'
                , uploadRetryTitle: 'Retry upload'
                , downloadIcon: '<i class="fas fa-download"></i>'
                , downloadClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary'
                , downloadTitle: 'Download file'
                , zoomIcon: '<i class="fas fa-search-plus"></i>'
                , zoomClass: 'btn btn-sm btn-kv btn-default btn-outline-secondary'
                , zoomTitle: 'View Details'
                , dragIcon: '<i class="fas fa-arrows-alt"></i>'
                , dragClass: 'text-info'
                , dragTitle: 'Move / Rearrange'
                , dragSettings: {}
                , indicatorNew: '<i class="glyphicon glyphicon-plus-sign text-warning"></i>'
                , indicatorSuccess: '<i class="glyphicon glyphicon-ok-sign text-success"></i>'
                , indicatorError: '<i class="glyphicon glyphicon-exclamation-sign text-danger"></i>'
                , indicatorLoading: '<i class="glyphicon glyphicon-hourglass text-muted"></i>'
                , indicatorPaused: '<i class="glyphicon glyphicon-pause text-primary"></i>'
                , indicatorNewTitle: 'Not uploaded yet'
                , indicatorSuccessTitle: 'Uploaded'
                , indicatorErrorTitle: 'Upload Error'
                , indicatorLoadingTitle: 'Uploading ...'
                , indicatorPausedTitle: 'Upload Paused'
            }
            , uploadIcon: '<i class="fas fa-upload"></i>'
            , removeIcon: '<i class="fas fa-trash"></i>'
            , overwriteInitial: false,
            // initialPreview: [],          // if you have previously uploaded preview files
            // initialPreviewConfig: [],    // if you have previously uploaded preview files
            deleteUrl: "http://localhost/file-delete.php"
            , progressClass: 'determinate progress-bar bg-success progress-bar-success progress-bar-striped active'
            , progressInfoClass: 'determinate progress-bar bg-info progress-bar-info progress-bar-striped active'
            , progressCompleteClass: 'determinate progress-bar bg-success progress-bar-success'
            , progressPauseClass: 'determinate progress-bar bg-primary progress-bar-primary progress-bar-striped active'
            , progressErrorClass: 'determinate progress-bar bg-danger progress-bar-danger'
        , }).on('fileuploaded', function(event, previewId, index, fileId) {
            uploadCallback(event, previewId, index, fileId)
        });
    });

</script>
@endsection
