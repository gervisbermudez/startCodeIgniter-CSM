@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection
@section('content')
	<div class="container form" id="root">
		<div class="row">
			<div class="col s12">
			<h3 class="page-header">{{$h1}}</h3>
			</div>
		</div>
		<div class="row">
			<div class="col s12 center" v-bind:class="{ hide: !loader }">
			<preloader />
			</div>
			<div id="form" class="col s12" v-bind:class="{'m10': user_id}" v-cloak v-show="!loader">
				<input type="hidden" name="id_form" value="">
				<span class="header grey-text text-darken-2">{{ lang('fragments_basic_data') }} <i
						class="material-icons left">description</i></span>
				<div class="input-field">
					<label for="nombre">{{ lang('fragments_name') }}</label>
					<input type="text" v-model="form.fields.name.value" id="nombre" name="nombre_form" required="required"
						value="">
				</div>
				<div class="input-field" v-show="fragmentToken">
					<label class="active">{{ lang('fragments_copy_token') }}</label>
					<input type="text" :value="fragmentToken" readonly id="fragment-token" @focus="$event.target.select()">
					<p class="grey-text text-darken-1">{{ lang('fragments_token_hint') }}</p>
					<p class="grey-text text-darken-1" v-pre><?php echo htmlspecialchars(lang('fragments_for_developers'), ENT_QUOTES, 'UTF-8'); ?></p>
					<button type="button" class="btn-flat" @click="copyToken()">{{ lang('fragments_copy_token') }}</button>
				</div>
				<div id="introduction" class="section scrollspy">
					<label for="id_cazary">{{ lang('fragments_content') }}</label>
					<div class="input-field">
						<textarea id="id_cazary" name="descripcion_form"></textarea>
					</div>
					<br>
				</div>
				<div class="input-field">
					<select name="tipo_form" v-model="type">
						<option value="0" disabled>{{ lang('fragments_select') }}</option>
						<option v-for="fragment_type in fragment_types" :key="fragment_type" :value="fragment_type">
							@{{ fragment_type }}
						</option>
					</select>
					<label>{{ lang('fragments_type') }}</label>
					<p class="grey-text text-darken-1">{{ lang('fragments_type_hint') }}</p>
				</div>
				<br>
				{{ lang('fragments_publish') }}
				<br>
				<div class="input-field">
					<div class="switch">
						<label>
							{{ lang('fragments_not_published') }}
							<input type="checkbox" name="status_form" value="1" v-model="status">
							<span class="lever"></span>
							{{ lang('fragments_published') }}
						</label>
					</div>
				</div>
				<br><br>
				<div class="input-field" id="buttons">
					<a href="<?php echo base_url('admin/fragments/'); ?>" class="btn-flat">{{ lang('btn_cancel') }}</a>
					<button type="button" class="btn-flat" @click="openPreview()">{{ lang('fragments_preview') }}</button>
					<button type="button" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
						<span><i class="material-icons right">save</i> {{ lang('btn_save') }}</span>
					</button>
				</div>
			</div>
			<div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id"  v-show="!loader">
			<span class="header grey-text text-darken-2">{{ lang('fragments_additional') }} <i class="material-icons left">description</i></span>
				<p>
					<b>{{ lang('fragments_created_by') }}</b>:
					<user-info :user="user" />
				</p>
				<p>
					<b>{{ lang('fragments_created') }}</b>: <br>
					<span>@{{date_create}}</span> <br><br>
					<b>{{ lang('fragments_modified') }}</b>: <br>
					<span>@{{date_update}}</span>
				</p>
			</div>
		</div>
		<div id="fragmentPreviewModal" class="modal">
			<div class="modal-content">
				<p class="page-header">{{ lang('fragments_preview') }}</p>
				<div v-if="previewHtml" class="fragment-preview-html" v-html="previewHtml"></div>
				<p v-else>{{ lang('fragments_preview_empty') }}</p>
			</div>
			<div class="modal-footer">
				<a href="#!" class="modal-close btn-flat">{{ lang('btn_cancel') }}</a>
			</div>
		</div>
		<file-explorer-selector :uploader="'single'" :preselected="[]" :modal="'fileUploader'" :mode="'files'"
			:filter="'images'" :multiple="false" v-on:notify="onPickImage"></file-explorer-selector>
	</div>
	@include('admin.components.file_explorer_selector_component')
<script>
	const fragment_id = <?=json_encode($fragment_id ? $fragment_id : false);?>;
	const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script>
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
  fragments_token_copied: <?php echo json_encode(lang('fragments_token_copied')); ?>,
  fragments_rename_warning: <?php echo json_encode(lang('fragments_rename_warning')); ?>,
  fragments_preview_empty: <?php echo json_encode(lang('fragments_preview_empty')); ?>
});
</script>
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/vendors/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/FragmentNewForm.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
