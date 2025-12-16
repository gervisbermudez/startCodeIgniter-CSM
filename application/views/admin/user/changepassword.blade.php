@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
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
        <div v-cloak v-if="!loader" class="col s12 m10 l10">
            <div id="initialization" class="section scrollspy">
                <h4 class="section-header">Password</h4>
                <p>
					<b>User</b>: 
                    <br/>
					<user-info :user="user" />
                    <br/>
                    <br/>
                    <br/>
                    <br/>
				</p>
                <div class="input-field ">
                    <input maxlength="25" id="currentPassword" name="currentPassword" value="" type="password" @change="form.validateField('currentPassword')" :class="{ valid: form.fields.currentPassword.touched && form.fields.currentPassword.valid, invalid: !form.fields.currentPassword.valid }" v-model="form.fields.currentPassword.value" autocomplete="off">
                    <label>Actual Contraseña</label>
                    <span class="helper-text" :data-error="form.fields.currentPassword.errorText" data-success="Valid">Debe contener mayusculas, minuculas, numeros y un caracter especial</span>
                </div>
                <div class="input-field ">
                    <input maxlength="25" id="password" name="password" value="" type="password" @change="form.validateField('password')" :class="{ valid: form.fields.password.touched && form.fields.password.valid, invalid: !form.fields.password.valid }" v-model="form.fields.password.value" autocomplete="off">
                    <label>Contraseña</label>
                    <span class="helper-text" :data-error="form.fields.password.errorText" data-success="Valid">Debe contener mayusculas, minuculas, numeros y un caracter especial</span>
                </div>
                <div class="input-field ">
                    <input maxlength="25" id="password2" name="password2" value="" type="password" @change="form.validateField('password2')" :class="{ valid: form.fields.password2.touched && form.fields.password2.valid, invalid: !form.fields.password2.valid }" v-model="form.fields.password2.value" autocomplete="off">
                    <label>Repetir Contraseña</label>
                    <span class="helper-text" :data-error="form.fields.password2.errorText" data-success="Valid">Debe contener mayusculas, minuculas, numeros y un caracter especial</span>
                </div>
                <br>
                <br>
                <div class="row userform">
                    <div class="col s12" id="buttons">
                        <a href="<?php echo base_url('admin/users/'); ?>" class="btn btn-default waves-effect waves-teal btn-flat">Cancelar</a>
                        <button type="submit" class="btn btn-primary waves-effect waves-teal" :class="{disabled: !btnEnable}"  @click="save();">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const user_id = <?php echo json_encode($userdata ? $userdata->user_id : false); ?> ;
</script>
@endsection


@section('footer_includes')
<script src="<?= base_url('public/js/validateForm.min.js'); ?>"></script>
<script src="<?= base_url('public/js/components/changePassword.component.min.js'); ?>"></script>
@endsection
