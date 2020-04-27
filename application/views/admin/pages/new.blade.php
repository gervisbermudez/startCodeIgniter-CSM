@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container" id="root">
    <input type="hidden" name="editMode" id="editMode" value="{{$editMode}}">
    <input type="hidden" name="page_id" id="page_id" value="{{$page_id}}">
    <div class="row" id="form">
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
        <div v-cloak v-show="!loader" class="col s12 m8 l9">
            <span class="header grey-text text-darken-2">Datos básicos <i
                    class="material-icons left">assignment</i></span>
            <div class="input-field">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required="required" value=""
                    v-model="form.fields.title.value">
            </div>
            <div class="input-field">
                <label for="subtitle">SubTitle:</label>
                <input type="text" id="subtitle" name="subtitle" required="required" value=""
                    v-model="form.fields.subtitle.value">
            </div>
            <div class="input-field">
                <label for="path">Path:</label>
                <input type="text" id="path" name="path" required="required" value="" v-model="path"
                    @blur="setPath(path);">
            </div>
            <div id="introduction" class="section scrollspy">
                <span class="header grey-text text-darken-2">Contenido <i
                        class="material-icons left">description</i></span>
                <br>
                <div class="input-field">
                    <textarea id="id_cazary" name="content"></textarea>
                </div>
                <br>
            </div>
            <br>
        </div>
        <div v-cloak v-show="!loader" class="col s12 m4 l3">
            <span class="header grey-text text-darken-2">Publicar Pagina <i
                    class="material-icons left">assignment_turned_in</i></span>
            <div class="input-field">
                <div class="switch">
                    <label>
                        No publicado
                        <input type="checkbox" name="status_form" value="0" v-model="status">
                        <span class="lever"></span>
                        Publicado
                    </label>
                </div>
            </div>
            <div v-show="status">
                <b class="grey-text text-darken-2"><i class="material-icons left">remove_red_eye</i> Visibilidad</b>
                <p>
                    <label>
                        <input name="visibility" v-model="visibility" value="1" type="radio" checked />
                        <span>Publico</span>
                    </label>
                </p>
                <p>
                    <label>
                        <input class="red" name="visibility" v-model="visibility" value="2" type="radio" />
                        <span>Privada</span>
                    </label>
                </p>
                <b class="grey-text text-darken-2"><i class="material-icons left">date_range</i> Fecha de
                    Publicación</b>
                <p>
                    <label>
                        <input type="checkbox" checked v-model="publishondate" />
                        <span>Publicar inmediatamente</span>
                    </label>
                </p>
                <div v-show="!publishondate">
                    <b class="grey-text text-darken-2">Seleccionar fecha y hora:</b>
                    <br>
                    <div class="input-field">
                        <label for="datepublish">Fecha:</label>
                        <input type="text" class="datepicker" id="datepublish" name="datepublish" required="required"
                            value="" v-model="datepublish">
                    </div>
                    <div class="input-field">
                        <label for="timepublish">Hora:</label>
                        <input type="text" class="timepicker" id="timepublish" name="timepublish" required="required"
                            value="" v-model="timepublish">
                    </div>
                </div>
            </div>
            <br>
            <span class="header grey-text text-darken-2"><i class="material-icons left">list</i> Tipo</span>
            <br>
            <div class="input-field">
                <select v-model="pageType" name="template">
                    <option v-for="(item, index) in pageTypes" :key="index" :value="item">
                        @{{item.page_type_name | capitalize}}</option>
                </select>
                <label>Tipo</label>
            </div>
            <br>
            <span class="header grey-text text-darken-2"><i class="material-icons left">toc</i> Categoria</span>
            <br>
            <div class="input-field">
                <select v-model="categorie" id="categorie" name="template" v-on:change="getSubCategories">
                    <option value="0">Ninguna</option>
                    <option v-for="(item, index) in categories" :key="index" :value="item.categorie_id">
                        @{{item.name | capitalize}}</option>
                </select>
                <label>Categoria</label>
            </div>
            <br>
            <div class="input-field">
                <select v-model="subcategorie" id="subcategories" name="subcategories">
                    <option value="0">Ninguna</option>
                    <option v-for="(item, index) in subcategories" :key="index" :value="item.categorie_id">
                        @{{item.name | capitalize}}</option>
                </select>
                <label>Sub Categoria</label>
            </div>
            <br>
            <span class="header grey-text text-darken-2"><i class="material-icons left">layers</i> Template</span>
            <br>
            <div class="input-field">
                <select v-model="template" id="template" name="template">
                    <option value="2" v-for="(template, index) in templates" :key="index" :value="template">@{{template}}</option>
                </select>
                <label>Internal Template</label>
            </div>
            <br>
            <div class="input-field">
                <select v-model="layout" id="layout" name="layout">
                    <option value="2" v-for="(layout, index) in layouts" :key="index" :value="layout">@{{layout}}</option>
                </select>
                <label>Layout</label>
            </div>
        </div>
        <div v-cloak v-show="!loader" class="col s12">
            <div class="input-field" id="buttons">
                <a href="<?php echo base_url('admin/paginas/'); ?>" class="btn-flat">Cancelar</a>
                <button type="submit" class="btn btn-primary" @click="save" :class="{disabled: !btnEnable}">
                    <span v-if="!status"><i class="material-icons right">edit</i> Guardar Borrador</span>
                    <span v-if="status && btnEnable"><i class="material-icons right">publish</i> Publicar</span>
                    <span v-if="status && !btnEnable"><i class="material-icons right">publish</i> Publicar</span>
                </button>
                <a  class="btn bg-color3" :href="preview_link" v-if="!status && editMode" :class="{disabled: !btnEnable}">
                    <span><i class="material-icons right">launch</i> Preview</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('public/js/validateForm.min.js')}}"></script>
<script src="{{base_url('public/js/components/PageNewForm.min.js')}}"></script>
@endsection