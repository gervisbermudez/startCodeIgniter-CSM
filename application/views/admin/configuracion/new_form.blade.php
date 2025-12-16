@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/fileinput-master/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>">
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
            <span class="header grey-text text-darken-2">Datos básicos <i
                    class="material-icons left">description</i></span>
            <div class="input-field">
                <label for="nombre">Nombre:</label>
                <input type="text" v-model="form.fields.config_name.value" id="nombre" name="nombre_form"
                    required="required" value="">
            </div>
            <div class="input-field">
                <label for="label">Label:</label>
                <input type="text" v-model="config_label" id="label" name="config_label" required="required" value="">
            </div>
            <div id="introduction" class="section scrollspy">
                <label for="id_cazary">Descripcion de la Configuracion:</label>
                <div class="input-field">
                    <textarea id="id_cazary" v-model="config_description" name="descripcion_form"></textarea>
                </div>
                <br>
            </div>
            <div>
                <span class="header">Valores Permitidos</span>
                <div class="chips chips-autocomplete" id="tags"></div>
            </div>
            <br />
            <div class="input-field">
                <label for="config_value">Valor Inicial:</label>
                <input type="text" v-model="config_value" id="config_value" name="config_value" required="required"
                    value="">
            </div>
            <br>
            <div class="input-field">
                <select name="tipo_form" v-model="config_type">
                    <option value="0" disabled>Selecciona</option>
                    <option v-for="site_config_type in config_type_options" :key="site_config_type"
                        :value="site_config_type">
                        @{{ site_config_type | capitalize }}
                    </option>
                </select>
                <label>Categoria de Configuracion</label>
            </div>
            <br>
            <div class="input-field" v-if="type_value == 'boolean'">
                <select name="true_value" v-model="true_value">
                    <option value="0" disabled>Selecciona</option>
                    <option v-for="tag in getTags()" :key="tag" :value="tag">
                        @{{ tag | capitalize }}
                    </option>
                </select>
                <label>Valor True</label>
            </div>
            <br>

            <div class="input-field">
                <select name="type_value" v-model="type_value" v-on:change="onChangeTypeValue">
                    <option value="0" disabled>Selecciona</option>
                    <option v-for="type_values_option in type_values_options" :key="type_values_option"
                        :value="type_values_option">
                        @{{ type_values_option | capitalize }}
                    </option>
                </select>
                <label>Tipo de valor</label>
            </div>
            <br>

            <div class="input-field">
                <label for="max_lenght">Max lenght:</label>
                <input type="text" v-model="max_lenght" id="max_lenght" name="max_lenght" required="required"
                    value="50">
            </div>
            <br>

            <div class="input-field">
                <label for="min_lenght">Min lenght:</label>
                <input type="text" v-model="min_lenght" id="min_lenght" name="min_lenght" required="required" value="5">
            </div>
            <br>

            <div class="input-field">
                <select name="tipo_form" v-model="handle_as">
                    <option value="0" disabled>Selecciona</option>
                    <option v-for="handle_as_option in handle_as_options" :key="handle_as_option"
                        :value="handle_as_option">
                        @{{ handle_as_option | capitalize }}
                    </option>
                </select>
                <label>Manejar con:</label>
            </div>
            <br>

            <div class="input-field">
                <select name="tipo_form" v-model="input_type">
                    <option value="0" disabled>Tipo de Input</option>
                    <option v-for="input_type_option in input_type_options" :key="input_type_option"
                        :value="input_type_option">
                        @{{ input_type_option | capitalize }}
                    </option>
                </select>
                <label>Manejar con:</label>
            </div>
            <br>

            readonly
            <br>
            <div class="input-field">
                <div class="switch">
                    <label>
                        Off
                        <input type="checkbox" name="readonly" value="0" v-model="readonly">
                        <span class="lever"></span>
                        On
                    </label>
                </div>
            </div>
            <br><br>
            <br>
            Publicar Configuracion
            <br>
            <div class="input-field">
                <div class="switch">
                    <label>
                        No publicado
                        <input type="checkbox" name="status_form" value="1" v-model="status">
                        <span class="lever"></span>
                        Publicado
                    </label>
                </div>
            </div>
            <br><br>
            <div class="input-field" id="buttons">
                <a href="<?php echo base_url('admin/configuration/'); ?>" class="btn-flat">Cancelar</a>
                <button type="submit" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
                    <span><i class="material-icons right">edit</i> Guardar</span>
                </button>
            </div>
        </div>
        <div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id" v-show="!loader">
            <span class="header grey-text text-darken-2">Adicional <i class="material-icons left">description</i></span>
            <p>
                <b>Creado por</b>:
                <user-info :user="user" />
            </p>
            <p>
                <b>Creado</b>: <br>
                <span>@{{date_create}}</span> <br><br>
                <b>Modificado</b>: <br>
                <span>@{{date_update}}</span> <br><br>
                <b>Publicado</b>: <br>
                <span>@{{date_publish}}</span>
            </p>
        </div>
    </div>
</div>
<script>
const site_config_id = <?=json_encode($site_config_id ? $site_config_id : false);?>;
const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('public/js/validateForm.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/ConfigNewForm.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection