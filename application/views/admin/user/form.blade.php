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
        <div v-cloak v-if="!loader" class="col s12 m10 l10">
            <div id="initialization" class="section scrollspy">
                <h4 class="section-header">Perfil</h4>
                <br>
                <div class="input-field">
                    <input maxlength="25" type="text" id="username" name="username" value="" @change="validateField('username')" v-model="form.fields.username.value" :class="{ valid: form.fields.username.touched && form.fields.username.valid, invalid: !form.fields.username.valid }" autocomplete="off">
                    <label>Username</label>
                    <span class="helper-text" :data-error="form.fields.username.errorText" data-success="Valid"></span>
                </div>
                <div class="input-field ">
                    <input maxlength="25" id="password" name="password" value="" type="password" @change="form.validateField('password')" :class="{ valid: form.fields.password.touched && form.fields.password.valid, invalid: !form.fields.password.valid }" v-model="form.fields.password.value" autocomplete="off">
                    <label>Contraseña</label>
                    <span class="helper-text" :data-error="form.fields.password.errorText" data-success="Valid">Debe contener mayusculas, minuculas, numeros y un caracter especial</span>
                </div>
                <div class="input-field">
                    <input id="email" type="email" name="email" maxlength="255" @change="validateField('email')" :class="{ valid: form.fields.email.touched && form.fields.email.valid, invalid: !form.fields.email.valid }" v-model="form.fields.email.value" autocomplete="off">
                    <label>Email</label>
                    <span class="helper-text" :data-error="form.fields.email.errorText" data-success="Valid"></span>
                    <br>
                </div>
                <div class="input-field" :class="{ valid: form.fields.usergroup_id.touched && form.fields.usergroup_id.valid, invalid: !form.fields.usergroup_id.valid }">
                    <select name="usergroup_id" v-model="form.fields.usergroup_id.value" @change="form.validateField('usergroup_id')">
                        <option v-for="(item, index) in usergroups" :key="index" :value="item.usergroup_id">
                            @{{item.name}}</option>
                    </select>
                    <label>Tipo de usuario:</label>
                </div>
            </div>
            <div id="structure" class="section scrollspy">
                <h4 class="section-header">Información de Perfil</h4>
                <div class="input-field">
                    <input maxlength="20" id="nombre" type="text" name="nombre" value="" @change="form.validateField('nombre')" :class="{ valid: form.fields.nombre.touched && form.fields.nombre.valid, invalid: !form.fields.nombre.valid }" v-model="form.fields.nombre.value">
                    <label>Nombre:</label>
                    <span class="helper-text" :data-error="form.fields.nombre.errorText"></span>
                </div>
                <div class="input-field">
                    <input maxlength="20" type="text" id="apellido" name="apellido" value="" @change="form.validateField('apellido')" :class="{ valid: form.fields.apellido.touched && form.fields.apellido.valid, invalid: !form.fields.apellido.valid }" v-model="form.fields.apellido.value"><label for="apellido">Apellido:</label>
                    <span class="helper-text" :data-error="form.fields.apellido.errorText"></span>
                </div>
                <div class="input-field">

                    <input maxlength="200" type="text" id="direccion" name="direccion" value="" @change="form.validateField('direccion')" :class="{ valid: form.fields.direccion.touched && form.fields.direccion.valid, invalid: !form.fields.direccion.valid }" v-model="form.fields.direccion.value"><label for="direccion">Direccion:</label>
                    <span class="helper-text" :data-error="form.fields.direccion.errorText"></span>
                </div>
                <div class="input-field">
                    <input maxlength="50" type="text" id="telefono" name="telefono" value="" @change="form.validateField('telefono')" :class="{ valid: form.fields.telefono.touched && form.fields.telefono.valid, invalid: !form.fields.telefono.valid }" v-model="form.fields.telefono.value"><label for="telefono">Telefono:</label>
                    <span class="helper-text" :data-error="form.fields.telefono.errorText"></span>
                </div>
                <br><br>
                <div class="switch">
                    <label>
                        Bloqueado
                        <input v-model="status" type="checkbox" id="status" name="status" checked="checked">
                        <span class="lever"></span>
                        Permitido
                    </label>
                </div>
                <br>
                <br>
                <div class="row userform">
                    <div class="col s12" id="buttons">
                        <a href="<?php echo base_url('admin/usuarios/'); ?>" class="btn btn-default waves-effect waves-teal btn-flat">Cancelar</a>
                        <button type="submit" class="btn btn-primary waves-effect waves-teal" @click="save();">Guardar</button>
                    </div>
                </div>

                <input type="hidden" name="mode" value="" v-model="editMode">
                <input type="hidden" name="id" value="" v-model="user_id">
            </div>
        </div>
        <div v-cloak v-if="!loader" class="col hide-on-small-only m2 l2">
            <ul class="section table-of-contents tabs-wrapper">
                <li><a href="#initialization">Perfil</a></li>
                <li><a href="#structure">Información</a></li>
            </ul>
        </div>
    </div>
</div>
<script>
    const user_id = < ? = json_encode($userdata ? $userdata - > user_id : false); ? > ;

</script>
@endsection
