@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/lightbox2-master/dist/css/lightbox.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/file_explorer.min.css')?>">
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
                <input type="text" v-model="form.fields.name.value" name="nombre_form" required="required" value="">
            </div>
            <div id="introduction" class="section scrollspy">
                <label for="id_cazary">Descripcion del Album:</label>
                <div class="input-field">
                    <textarea id="id_cazary" v-model="content" name="content"></textarea>
                </div>
                <br>
            </div>
            <div class="row" v-if="items.length">
                <div class="col s12 m4" v-for="(album_item, index) in items" :key="index">
                    <div class="card">
                        <div class="card-image">
                            <div class="card-image-container">
                                <i v-if="album_item.status == 1" class="material-icons tooltipped" data-position="left"
                                    data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    data-tooltip="Privado">lock</i>
                                <img class="materialboxed" :src="getPageImagePath(album_item)" />
                            </div>
                            <a @click="removeItemImage(index);" class="btn-floating halfway-fab waves-effect waves-light" href='#!'>
                                <i class="material-icons">delete</i></a>
                        </div>
                        <div class="card-content">
                            <div class="input-field">
                                <label for="nombre">Nombre:</label>
                                <input type="text" v-model="album_item.name" name="nombre_form" required="required"
                                    value="">
                            </div>
                            <div class="card-info">
                                <div class="input-field">
                                    <textarea :id="'textarea1' + album_item.album_item_id" class="materialize-textarea"
                                        v-model="album_item.description"></textarea>
                                    <label for="textarea1">Description</label>
                                </div>
                                <div class="input-field">
                                    <div class="switch">
                                        <label>
                                            No publicado
                                            <input type="checkbox" name="status_form" value="1" v-model="album_item.status">
                                            <span class="lever"></span>
                                            Publicado
                                        </label>
                                    </div>
                                </div>
                                <ul>
                                    <li><b>Fecha de creacion:</b> <br>
                                        @{{album_item.date_create}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <a type="button" class="btn btn-primary modal-trigger" href="#folderSelector">
                    <span><i class="material-icons right">image</i> Agregar Imagenes</span>
                </a>
            </div>
            <br>
            Publicar Album
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
                <a href="<?php echo base_url('admin/categories/'); ?>" class="btn-flat">Cancelar</a>
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
    <file-explorer-selector 
    :uploader="'single'"
    :preselected="preselected" 
    :modal="'folderSelector'" 
    :mode="'files'" 
    :filter="'images'" 
    :multiple="true"
    v-on:notify="copyCallcack"
    ></file-explorer-selector>
</div>
<script>
    const album_id = <?= json_encode($album_id ? $album_id : false);?>;
    const editMode = <?= json_encode($editMode ? $editMode : 'new');?>;
</script>
@include('admin.components.FileExplorerSelector')
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<script src="{{base_url('public/js/lightbox2-master/dist/js/lightbox.min.js')}}"></script>
<script src="{{base_url('public/js/fileinput-master/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js')}}"></script>
<script src="{{base_url('public/js/fileinput-master/js/locales/es.js')}}"></script>
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/AlbumNewForm.js?v=' . ADMIN_VERSION)}}"></script>
@endsection