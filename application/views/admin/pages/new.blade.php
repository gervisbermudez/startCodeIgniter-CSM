@extends('admin.layouts.app') @section('title', $title)
@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/fileinput-master/css/fileinput.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>" />
    <script src="https://unpkg.com/react@16.6.3/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@16.6.3/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/moment@2.22.1/min/moment.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" crossorigin="anonymous"></script>
    <link href="{{base_url('public/js/GutenbergEditor')}}/static/css/2.71437e52.chunk.css" rel="stylesheet">
@endsection @section('content')
<div class="container form" id="PageNewForm-root">
    <input type="hidden" name="editMode" id="editMode" value="{{ $editMode }}" />
    <input type="hidden" name="page_id" id="page_id" value="{{ $page_id }}" />
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{ $h1 }}</h3>
        </div>
    </div>
    <div class="row" id="form">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <preloader />
        </div>
        <div class="col s12">
            <ul class="tabs" id="formTabs">
                <li class="tab col s3">
                    <a class="active grey-text text-darken-2" href="#test1">
                        <i class="material-icons">assignment</i><span>Datos básicos </span>
                    </a>
                </li>
                <li class="tab col s3">
                    <a href="#test2" class="active grey-text text-darken-2">
                        <i class="material-icons">build</i>
                        <span>Adicional</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col s12" id="test1">
            <div v-cloak v-show="!loader" class="col s12 m8 l9">
                <div class="input-field">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required="required" value="" v-model="form.fields.title.value" v-on:blur="onChangeTitle(form.fields.title.value);" />
                </div>
                <div class="input-field">
                    <label for="subtitle">SubTitle:</label>
                    <input type="text" id="subtitle" name="subtitle" required="required" value="" v-model="form.fields.subtitle.value" />
                </div>
                <div class="input-field">
                    <label for="path">Path:</label>
                    <input type="text" id="path" name="path" required="required" value="" v-model="path" />
                    <br />
                </div>
                <p>
                    <span><b>Full path:</b> @{{ getPagePath }}</span>
                </p>
                <a href="#fileUploader" class="waves-effect waves-light btn modal-trigger"><i class="material-icons left">add_a_photo</i>Agregar Imagen</a>
                <div class="row" v-if="mainImage">
                    <div class="col s6" v-for="(image, index) in mainImage" :key="index">
                        <div class="card mainPageImage">
                            <div class="card-image">
                                <i class="material-icons right close tooltipped" data-position="left" data-delay="50" data-tooltip="Quitar" v-on:click="removeImage(index);">close</i>
                                <img class="materialboxed" :src="getFileImagenPath(image)" />
                            </div>
                            <span class="card-title truncate"><span>@{{ getFileImagenName(image) }}</span></span>
                        </div>
                    </div>
                </div>
                <div id="introduction" class="section scrollspy">
                    <span class="header grey-text text-darken-2">Contenido <i class="material-icons left">description</i></span>
                    <br />
                    <div id="root"></div>
                    <br />
                </div>
                <br />
            </div>
            <div v-cloak v-show="!loader" class="col s12 m4 l3">
                <span class="header grey-text text-darken-2">Publicar Pagina
                    <i class="material-icons left">assignment_turned_in</i></span>
                <div class="input-field">
                    <div class="switch">
                        <label>
                            No publicado
                            <input type="checkbox" name="status_form" value="0" v-model="status" />
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
                        <br />
                        <div class="input-field">
                            <label for="datepublish">Fecha:</label>
                            <input type="text" class="datepicker" id="datepublish" name="datepublish" required="required" value="" v-model="datepublish" />
                        </div>
                        <div class="input-field">
                            <label for="timepublish">Hora:</label>
                            <input type="text" class="timepicker" id="timepublish" name="timepublish" required="required" value="" v-model="timepublish" />
                        </div>
                    </div>
                </div>
                <br />
                <span class="header grey-text text-darken-2"><i class="material-icons left">list</i> Tipo</span>
                <br />
                <div class="input-field">
                    <select v-model="page_type_id" name="template" @blur="setPath();">
                        <option v-for="(item, index) in pageTypes" :key="index" :value="item.page_type_id">
                            @{{ item.page_type_name | capitalize }}
                        </option>
                    </select>
                    <label>Tipo</label>
                </div>
                <br />
                <span class="header grey-text text-darken-2"><i class="material-icons left">toc</i> Categoria</span>
                <br />
                <div class="input-field">
                    <select v-model="categorie_id" id="categorie" name="template" v-on:change="getSubCategories">
                        <option value="0">Ninguna</option>
                        <option v-for="(item, index) in categories" :key="index" :value="item.categorie_id">
                            @{{ item.name | capitalize }}
                        </option>
                    </select>
                    <label>Categoria</label>
                </div>
                <br />
                <div class="input-field">
                    <select v-model="subcategorie_id" id="subcategories" name="subcategories">
                        <option value="0">Ninguna</option>
                        <option v-for="(item, index) in subcategories" :key="index" :value="item.categorie_id">
                            @{{ item.name | capitalize }}
                        </option>
                    </select>
                    <label>Sub Categoria</label>
                </div>
                <br />
                <span class="header grey-text text-darken-2"><i class="material-icons left">layers</i> Template</span>
                <br />
                <div class="input-field">
                    <select v-model="template" id="template" name="template">
                        <option value="2" v-for="(template, index) in templates" :key="index" :value="template">
                            @{{ template }}
                        </option>
                    </select>
                    <label>Internal Template</label>
                </div>
                <br />
                <div class="input-field">
                    <select v-model="layout" id="layout" name="layout">
                        <option value="2" v-for="(layout, index) in layouts" :key="index" :value="layout">
                            @{{ layout }}
                        </option>
                    </select>
                    <label>Layout</label>
                </div>
                <div v-if="user">
                    <p><b>Creado por</b>:</p>
                    <ul class="collection form-user-component">
                        <li class="collection-item avatar">
                            <a :href="user.get_profileurl()">
                                <img :src="user.get_avatarurl()" alt="" class="circle" />
                                <span class="title">@{{ user.get_fullname() }}</span>
                                <p>@{{ user.usergroup.name }}</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="test2" class="col s12">
            <div class="col s12 m8 l9" v-cloak v-show="!loader">
                <br />
                <span class="header grey-text text-darken-2">Agregar Etiquetas</span>
                <div class="chips chips-autocomplete" id="pageTags"></div>
            </div>
            <br />
            <div class="row" v-cloak v-show="!loader">
                <div class="input-field col s12">
                    <textarea id="title" v-model="page_data.title" class="materialize-textarea"></textarea>
                    <label for="title">Page title</label>
                </div>
                <div class="input-field col s12">
                    <textarea id="headers_includes" v-model="page_data.headers_includes" class="materialize-textarea"></textarea>
                    <label for="headers_includes">headers includes</label>
                </div>
                <div class="input-field col s12">
                    <textarea id="footer_includes" v-model="page_data.footer_includes" class="materialize-textarea"></textarea>
                    <label for="footer_includes">Footer includes</label>
                </div>
            </div>
            <div class="row" v-cloak v-show="!loader">
                <div class="col s12">
                    <span class="header grey-text text-darken-2"><i class="material-icons left">layers</i> Page Metas <a class="waves-effect waves-light btn right" href="#!" @click="addCustomMeta();">Agregar meta +</a></span>
                    <br />
                    <ul class="collapsible" id="pageMetas">
                        <li v-for="(meta, index) in customMetas">
                            <div class="collapsible-header">
                                <i class="material-icons">filter_drama</i>
                                <input placeholder="name" v-model="meta.name" type="text" class="validate">
                                <i class="material-icons right remove" @click="removeMeta(index, true);">do_not_disturb_on</i>
                            </div>
                            <div class="collapsible-body">
                                <span v-if="meta.content">@{{meta.content}}</span>
                                <div class="input-field">
                                    <input placeholder="content" v-model="meta.content" type="text" class="validate">
                                </div>
                            </div>
                        </li>
                        <li v-for="(meta, index) in metas">
                            <div class="collapsible-header">
                                <i class="material-icons">filter_drama</i>
                                <span v-if="meta.name">@{{meta.name}}</span>
                                <span v-if="meta.property">@{{meta.property}}</span>
                                <i class="material-icons right remove" @click="removeMeta(index, false);">do_not_disturb_on</i>
                            </div>
                            <div class="collapsible-body">
                                <span v-if="meta.content">@{{meta.content}}</span>
                                <div class="input-field">
                                    <input placeholder="content" v-model="meta.content" type="text" class="validate">
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
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
                <a class="btn bg-color3" target="_blank" :href="preview_link" v-if="!status && editMode" :class="{disabled: !btnEnable}">
                    <span><i class="material-icons right">launch</i> Preview</span>
                </a>
            </div>
        </div>
    </div>
    <file-explorer-selector 
    :uploader="'single'"
    :preselected="[]" 
    :modal="'fileUploader'" 
    :mode="'files'" 
    :filter="'images'" 
    :multiple="true"
    v-on:notify="copyCallcack"
    ></file-explorer-selector>    
</div>
@include('admin.components.FileExplorerSelector')
@endsection

@section('footer_includes')
<script src="{{ base_url('public/js/validateForm.min.js') }}"></script>
<script src="{{base_url('public/js/components/FileExplorerSelector.min.js')}}"></script>
<script src="{{base_url('public/js/components/PageNewForm.min.js')}}"></script>
<script src="<?=base_url('public/js/fileinput-master/js/fileinput.min.js');?>"></script>
<script src="<?=base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js');?>"></script>
<script src="<?=base_url('public/js/fileinput-master/js/locales/es.js');?>"></script>
<script>!function (e) { function r(r) { for (var n, a, l = r[0], c = r[1], p = r[2], i = 0, s = []; i < l.length; i++)a = l[i], Object.prototype.hasOwnProperty.call(o, a) && o[a] && s.push(o[a][0]), o[a] = 0; for (n in c) Object.prototype.hasOwnProperty.call(c, n) && (e[n] = c[n]); for (f && f(r); s.length;)s.shift()(); return u.push.apply(u, p || []), t() } function t() { for (var e, r = 0; r < u.length; r++) { for (var t = u[r], n = !0, l = 1; l < t.length; l++) { var c = t[l]; 0 !== o[c] && (n = !1) } n && (u.splice(r--, 1), e = a(a.s = t[0])) } return e } var n = {}, o = { 1: 0 }, u = []; function a(r) { if (n[r]) return n[r].exports; var t = n[r] = { i: r, l: !1, exports: {} }; return e[r].call(t.exports, t, t.exports, a), t.l = !0, t.exports } a.m = e, a.c = n, a.d = function (e, r, t) { a.o(e, r) || Object.defineProperty(e, r, { enumerable: !0, get: t }) }, a.r = function (e) { "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(e, "__esModule", { value: !0 }) }, a.t = function (e, r) { if (1 & r && (e = a(e)), 8 & r) return e; if (4 & r && "object" == typeof e && e && e.__esModule) return e; var t = Object.create(null); if (a.r(t), Object.defineProperty(t, "default", { enumerable: !0, value: e }), 2 & r && "string" != typeof e) for (var n in e) a.d(t, n, function (r) { return e[r] }.bind(null, n)); return t }, a.n = function (e) { var r = e && e.__esModule ? function () { return e.default } : function () { return e }; return a.d(r, "a", r), r }, a.o = function (e, r) { return Object.prototype.hasOwnProperty.call(e, r) }, a.p = "/"; var l = this["webpackJsonpreact-ea6cpb"] = this["webpackJsonpreact-ea6cpb"] || [], c = l.push.bind(l); l.push = r, l = l.slice(); for (var p = 0; p < l.length; p++)r(l[p]); var f = c; t() }([])</script>
<script src="{{base_url('public/js/GutenbergEditor')}}/static/js/2.dadeb995.chunk.js"></script>
<script src="{{base_url('public/js/GutenbergEditor')}}/static/js/main.8ef91553.chunk.js"></script>
@endsection