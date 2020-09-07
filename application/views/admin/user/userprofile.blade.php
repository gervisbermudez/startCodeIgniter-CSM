@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
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
		<div class="col s12" v-cloak v-if="!loader">
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
						<img :src="getAvatarUrl(user.user_data.avatar)" :alt="user.username" class="circle z-depth-1">
					</a>
					<a href="#fileUploader" class="modal-trigger" v-else>
						<i class="material-icons circle grey lighten-5 z-depth-1">account_circle</i></a>
				</div>
				<div class="card-content row">
					<div class="col s12 m6">
						<span class="card-title">@{{user.user_data.nombre}} @{{user.user_data.apellido}}</span>
						<p>
							@{{user.role}}
						</p>
					</div>
					<div class="col s12 m6">
						<span class="card-title">@{{user.username}}</span>
						<p>
							Ultima vez: @{{user.lastseen}}
						</p>
					</div>


				</div>
			</div>
		</div>
		<div class="col s12 m5" v-cloak v-show="!loader">
			<ul class="collection with-header">
				<li class="collection-header">
					<h4>Datos del usuario</h4>
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
		<div class="col s12 m7 timeline"></div>
	</div>
</div>
@include('admin.components.fileUploaderComponent')
@endsection

@section('footer_includes')
<script src="<?= base_url('public/js/components/fileUploaderModule.min.js'); ?>"></script>
<script src="<?= base_url('public/js/components/userProfileComponent.min.js'); ?>"></script>
<script src="<?= base_url('public/js/fileinput-master/js/fileinput.min.js'); ?>"></script>
<script src="<?= base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js'); ?>"></script>
<script src="<?= base_url('public/js/fileinput-master/js/locales/es.js'); ?>"></script>
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
				'uploadToken': 'SOME-TOKEN',
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
		}).on('fileuploaded', function (event, previewId, index, fileId) { uploadCallback(event, previewId, index, fileId) });
	});
</script>
@endsection