@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection

@section('content')
<div class="container form page-form user-form" id="root">
    <div class="row">
        <div class="col s12">
            <h1 class="page-header">{{ $h1 }}</h1>
            <p class="user-form__lede"><?= $userdata ? lang('users_form_lede_edit') : lang('users_form_lede') ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <preloader />
        </div>
    </div>
    <form v-cloak v-if="!loader" class="user-form__body" @submit.prevent="save">
        <div class="row">
            <div class="col s12 m8 l9">
                <section class="user-form__section" id="account" aria-labelledby="user-form-account">
                    <h2 class="user-form__section-title" id="user-form-account"><?= lang('users_form_account') ?></h2>
                    <p class="user-form__section-help"><?= lang('users_form_required_hint') ?></p>
                    <div class="input-field">
                        <input maxlength="18" type="text" id="username" name="username" @change="validateField('username')" v-model="form.fields.username.value" :class="{ valid: form.fields.username.touched && form.fields.username.valid, invalid: form.fields.username.touched && !form.fields.username.valid }" autocomplete="off" required>
                        <label for="username"><?= lang('username') ?></label>
                        <span class="helper-text" :data-error="form.fields.username.errorText"><?= lang('users_form_username_help') ?></span>
                    </div>
                    <div class="input-field">
                        <input id="email" type="email" name="email" maxlength="255" @change="validateField('email')" :class="{ valid: form.fields.email.touched && form.fields.email.valid, invalid: form.fields.email.touched && !form.fields.email.valid }" v-model="form.fields.email.value" autocomplete="off" required>
                        <label for="email"><?= lang('email') ?></label>
                        <span class="helper-text" :data-error="form.fields.email.errorText"></span>
                    </div>
                    <div class="input-field" v-if="!editMode">
                        <input maxlength="25" id="password" name="password" :type="showPassword ? 'text' : 'password'" @change="form.validateField('password')" :class="{ valid: form.fields.password.touched && form.fields.password.valid, invalid: form.fields.password.touched && !form.fields.password.valid }" v-model="form.fields.password.value" autocomplete="new-password" required>
                        <label for="password"><?= lang('password') ?></label>
                        <button type="button" class="user-form-password__toggle btn-flat waves-effect" @click="showPassword = !showPassword" :aria-label='showPassword ? <?= json_encode(lang('users_form_hide_password')) ?> : <?= json_encode(lang('users_form_show_password')) ?>'>
                            <i class="material-icons" aria-hidden="true">@{{ showPassword ? 'visibility_off' : 'visibility' }}</i>
                        </button>
                        <span class="helper-text" :data-error="form.fields.password.errorText"><?= lang('users_form_password_help') ?></span>
                        <button type="button" class="btn-flat waves-effect user-form-password__generate" @click="generatePassword"><?= lang('users_form_generate_password') ?></button>
                    </div>
                    <p class="user-form__hint" v-else>
                        <a :href="changePasswordUrl"><?= lang('users_form_change_password') ?></a>
                    </p>
                    <div class="input-field">
                        <select id="usergroup_id" name="usergroup_id" v-model="form.fields.usergroup_id.value" @change="form.validateField('usergroup_id')">
                            <option value="" disabled><?= lang('users_form_usergroup_placeholder') ?></option>
                            <option v-for="item in usergroups" :key="item.usergroup_id" :value="item.usergroup_id">@{{ item.name }}</option>
                        </select>
                        <label for="usergroup_id"><?= lang('users_form_usergroup') ?></label>
                        <span class="helper-text"><?= lang('users_form_usergroup_help') ?></span>
                    </div>
                </section>

                <section class="user-form__section" id="profile" aria-labelledby="user-form-profile">
                    <h2 class="user-form__section-title" id="user-form-profile">
                        <?= lang('users_form_profile') ?>
                        <span class="user-form__optional"><?= lang('users_form_optional') ?></span>
                    </h2>
                    <div class="user-form-avatar">
                        <a href="#folderSelector" class="user-form-avatar__pick modal-trigger" aria-label="<?= htmlspecialchars(lang('users_form_avatar_choose'), ENT_QUOTES, 'UTF-8') ?>">
                            <img v-if="avatarUrl" :src="avatarUrl" alt="">
                            <i v-else class="material-icons" aria-hidden="true">person</i>
                        </a>
                        <div class="user-form-avatar__actions">
                            <span class="user-form-avatar__label"><?= lang('users_form_avatar') ?></span>
                            <p class="user-form-avatar__help"><?= lang('users_form_avatar_help') ?></p>
                            <a href="#folderSelector" class="btn-flat waves-effect modal-trigger">
                                <span v-if="avatar"><?= lang('users_form_avatar_change') ?></span>
                                <span v-else><?= lang('users_form_avatar_choose') ?></span>
                            </a>
                            <button type="button" class="btn-flat waves-effect" v-if="avatar" @click="clearAvatar"><?= lang('users_form_avatar_remove') ?></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m6">
                            <div class="input-field">
                                <input maxlength="40" id="nombre" type="text" name="nombre" @change="validateOptionalField('nombre')" :class="{ valid: form.fields.nombre.touched && form.fields.nombre.valid, invalid: form.fields.nombre.touched && !form.fields.nombre.valid }" v-model="form.fields.nombre.value" autocomplete="given-name">
                                <label for="nombre"><?= lang('first_name') ?></label>
                                <span class="helper-text" :data-error="form.fields.nombre.errorText"></span>
                            </div>
                        </div>
                        <div class="col s12 m6">
                            <div class="input-field">
                                <input maxlength="40" type="text" id="apellido" name="apellido" @change="validateOptionalField('apellido')" :class="{ valid: form.fields.apellido.touched && form.fields.apellido.valid, invalid: form.fields.apellido.touched && !form.fields.apellido.valid }" v-model="form.fields.apellido.value" autocomplete="family-name">
                                <label for="apellido"><?= lang('last_name') ?></label>
                                <span class="helper-text" :data-error="form.fields.apellido.errorText"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <input maxlength="80" type="text" id="cargo" name="cargo" @change="validateOptionalField('cargo')" :class="{ valid: form.fields.cargo.touched && form.fields.cargo.valid, invalid: form.fields.cargo.touched && !form.fields.cargo.valid }" v-model="form.fields.cargo.value">
                        <label for="cargo"><?= lang('users_form_job_title') ?></label>
                        <span class="helper-text" :data-error="form.fields.cargo.errorText"></span>
                    </div>
                    <div class="row">
                        <div class="col s12 m6">
                            <div class="input-field">
                                <input maxlength="18" type="tel" id="telefono" name="telefono" @change="validateOptionalField('telefono')" :class="{ valid: form.fields.telefono.touched && form.fields.telefono.valid, invalid: form.fields.telefono.touched && !form.fields.telefono.valid }" v-model="form.fields.telefono.value" autocomplete="tel">
                                <label for="telefono"><?= lang('users_form_phone') ?></label>
                                <span class="helper-text" :data-error="form.fields.telefono.errorText"></span>
                            </div>
                        </div>
                        <div class="col s12 m6">
                            <div class="input-field">
                                <input maxlength="60" type="text" id="direccion" name="direccion" @change="validateOptionalField('direccion')" :class="{ valid: form.fields.direccion.touched && form.fields.direccion.valid, invalid: form.fields.direccion.touched && !form.fields.direccion.valid }" v-model="form.fields.direccion.value" autocomplete="street-address">
                                <label for="direccion"><?= lang('users_form_address') ?></label>
                                <span class="helper-text" :data-error="form.fields.direccion.errorText"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-field">
                        <textarea id="bio" name="bio" class="materialize-textarea" maxlength="400" @change="validateOptionalField('bio')" :class="{ valid: form.fields.bio.touched && form.fields.bio.valid, invalid: form.fields.bio.touched && !form.fields.bio.valid }" v-model="form.fields.bio.value"></textarea>
                        <label for="bio"><?= lang('users_form_bio') ?></label>
                        <span class="helper-text" :data-error="form.fields.bio.errorText"><?= lang('users_form_bio_help') ?></span>
                    </div>
                </section>
            </div>
            <div class="col s12 m4 l3">
                <aside class="user-form__aside">
                    <h2 class="user-form__section-title"><?= lang('users_form_aside_title') ?></h2>
                    <p><?= lang('users_form_aside_body') ?></p>
                    <p v-if="editMode">
                        <a :href="profileUrl"><?= lang('users_form_view_profile') ?></a>
                    </p>
                </aside>
            </div>
        </div>
        <div class="page-form-actions">
            <a href="<?= base_url('admin/users/') ?>" class="btn-flat waves-effect"><?= lang('btn_cancel') ?></a>
            <button type="submit" class="btn waves-effect page-form-save" :class="{ disabled: !canSave }" :disabled="!canSave">
                <span v-if="!editMode"><?= lang('users_form_create') ?></span>
                <span v-else><?= lang('btn_save') ?></span>
            </button>
        </div>
    </form>
    <file-explorer-selector
        :uploader="'single'"
        :preselected="[]"
        :modal="'folderSelector'"
        :mode="'files'"
        :filter="'images'"
        :multiple="false"
        :initialdir="avatarDir"
        v-on:notify="onPickAvatar"
    ></file-explorer-selector>
</div>
@include('admin.components.file_explorer_selector_component')
<script>
    const user_id = <?php echo json_encode($userdata ? $userdata->user_id : false); ?>;
</script>
@endsection

@section('footer_includes')
<script>
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
    users_form_field_taken: <?php echo json_encode(lang('users_form_field_taken')); ?>,
    users_form_password_generated: <?php echo json_encode(lang('users_form_password_generated')); ?>,
    users_form_show_password: <?php echo json_encode(lang('users_form_show_password')); ?>,
    users_form_hide_password: <?php echo json_encode(lang('users_form_hide_password')); ?>
});
</script>
<script src="{{base_url('public/vendors/fileinput/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/plugins/canvas-to-blob.min.js')}}"></script>
<script src="{{base_url('resources/components/FileExplorerSelector.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/UserNewForm.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
