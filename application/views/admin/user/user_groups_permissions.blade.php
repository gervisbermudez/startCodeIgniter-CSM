@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection
@section('content')
<div class="container form" id="root">
	<div class="row">
		<div class="col s12">
			<p class="page-header">{{ $h1 }}</p>
		</div>
	</div>
	<div class="row">
		<div class="col s12 center" v-bind:class="{ hide: !loader }">
			<preloader />
		</div>
		<div id="form" class="col s12" v-cloak v-show="!loader">
			<span class="header grey-text text-darken-2">{{ lang('usergroups_basic') }}</span>
			<br>
			<div class="input-field">
				<input type="text" v-model="name" id="usergroup-name" name="usergroup_name" required="required">
				<label for="usergroup-name">{{ lang('name') }}</label>
			</div>
			<div class="input-field">
				<input type="text" v-model="description" id="usergroup-description" name="usergroup_description" required="required">
				<label for="usergroup-description">{{ lang('description') }}</label>
			</div>
			<p class="page-header">{{ lang('usergroups_permissions') }}</p>
			<div class="row" v-for="mod in permissionModules" :key="mod.module">
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							<span class="card-title">@{{ moduleTitle(mod.module) }}</span>
							<p>
								<label>
									<input type="checkbox"
										:checked="moduleAllChecked(mod.module)"
										:disabled="!moduleHasEditable(mod.module)"
										@change="toggleModule(mod.module, $event.target.checked)"
									/>
									<span>{{ lang('usergroups_check_module') }}</span>
								</label>
							</p>
							<div class="row">
								<div class="col s12 m6 l4" v-for="permission in mod.items" :key="permission.permisions_id">
									<p>
										<label
											:class="{ 'grey-text': permission.locked, tooltipped: permission.locked }"
											:for="'perm-' + permission.permisions_id"
											:data-tooltip="permission.locked ? lang('usergroups_perm_locked') : null"
											data-position="top"
										>
											<input
												type="checkbox"
												:id="'perm-' + permission.permisions_id"
												:disabled="permission.locked"
												v-model="permission.enabled"
											/>
											<span>@{{ permission.label }}</span>
										</label>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="input-field">
				<div class="switch">
					<label>
						{{ lang('usergroups_inactive') }}
						<input type="checkbox" id="usergroup-status" name="status_form" v-model="status">
						<span class="lever"></span>
						{{ lang('usergroups_active') }}
					</label>
				</div>
			</div>
			<div class="input-field" id="buttons">
				<a href="<?php echo base_url('admin/users/usergroups'); ?>" class="btn-flat">{{ lang('cancel') }}</a>
				<button type="button" class="btn" @click="save()" :class="{disabled: !btnEnable || loader}" :disabled="!btnEnable || loader">
					<span>{{ lang('save') }}</span>
				</button>
			</div>
		</div>
	</div>
</div>
<script>
	const usergroup_id = <?= json_encode($usergroup_id ? $usergroup_id : false); ?>;
	const editMode = <?= json_encode($editMode ? $editMode : 'new'); ?>;
	const editorPermisions = <?= json_encode(userdata('usergroup_permisions') ?: array()); ?>;
	window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
		name: <?= json_encode(lang('name')) ?>,
		description: <?= json_encode(lang('description')) ?>,
		save: <?= json_encode(lang('save')) ?>,
		cancel: <?= json_encode(lang('cancel')) ?>,
		usergroups_basic: <?= json_encode(lang('usergroups_basic')) ?>,
		usergroups_permissions: <?= json_encode(lang('usergroups_permissions')) ?>,
		usergroups_check_module: <?= json_encode(lang('usergroups_check_module')) ?>,
		usergroups_perm_locked: <?= json_encode(lang('usergroups_perm_locked')) ?>,
		usergroups_active: <?= json_encode(lang('usergroups_active')) ?>,
		usergroups_inactive: <?= json_encode(lang('usergroups_inactive')) ?>,
		usergroups_saved: <?= json_encode(lang('usergroups_saved')) ?>,
		usergroups_unexpected_error: <?= json_encode(lang('usergroups_unexpected_error')) ?>,
		toast_saved: <?= json_encode(lang('toast_saved')) ?>,
		toast_error: <?= json_encode(lang('usergroups_unexpected_error')) ?>,
		perm_module_users: <?= json_encode(lang('perm_module_users')) ?>,
		perm_module_pages: <?= json_encode(lang('perm_module_pages')) ?>,
		perm_module_form_custom: <?= json_encode(lang('perm_module_form_custom')) ?>,
		perm_module_menu: <?= json_encode(lang('perm_module_menu')) ?>,
		perm_module_file: <?= json_encode(lang('perm_module_file')) ?>,
		perm_module_categories: <?= json_encode(lang('perm_module_categories')) ?>,
		perm_module_content_data: <?= json_encode(lang('perm_module_content_data')) ?>,
		perm_module_config: <?= json_encode(lang('perm_module_config')) ?>,
		perm_module_events: <?= json_encode(lang('perm_module_events')) ?>,
		perm_module_analytics: <?= json_encode(lang('perm_module_analytics')) ?>,
		perm_module_siteforms: <?= json_encode(lang('perm_module_siteforms')) ?>,
		perm_module_fragments: <?= json_encode(lang('perm_module_fragments')) ?>,
		perm_module_gallery: <?= json_encode(lang('perm_module_gallery')) ?>,
		perm_module_videos: <?= json_encode(lang('perm_module_videos')) ?>,
		perm_module_calendar: <?= json_encode(lang('perm_module_calendar')) ?>
	});
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/UserPermissionsForm.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
