@extends('admin.layouts.app') @section('title', $title)
@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/fileinput-master/css/fileinput.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>" />
@endsection @section('content')
<div class="container form" id="PageNewForm-root">
    <input type="hidden" name="editMode" id="editMode" value="{{ $editMode }}" />
    <input type="hidden" name="page_id" id="page_id" value="{{ $page_id }}" />
    <div class="row" v-cloak>
        <div class="col s12">
            <h3 class="page-header">@{{ form.fields.title.value }}</h3>
            <h6>@{{form.fields.subtitle.value}}</h6>
        </div>
    </div>
    <div class="row" id="form">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <preloader />
        </div>
        <div class="col s12" id="test1">
            <div v-cloak v-show="!loader" class="col s12" v-bind:class="{ l9: !fullView }">
                <div class="row" v-if="mainImage">
                    <div class="col s12">
                        <div class="card mainPageImage" v-for="(image, index) in mainImage" :key="index">
                            <div class="card-image">
                                <i class="material-icons right close tooltipped" data-position="left" data-delay="50" data-tooltip="Quitar" v-on:click="removeImage(index);">close</i>
                                <img :src="getFileImagenPath(image)" />
                                <span class="card-title">@{{ getFileImagenName(image) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="introduction" class="section">
                    <div id="root">
                        <preview :url="preview_link"
                        v-on:expand="expandView"
                        ></preview>
                    </div>
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
                <div v-cloak v-show="!loader">
            <div class="input-field" id="buttons">
                <a href="<?php echo base_url('admin/paginas/'); ?>" class="btn-flat">Cancelar</a>
                <a href="<?php echo base_url('admin/paginas/editar/' . $page_id); ?>" class="waves-effect waves-light btn"><i class="material-icons left">create</i>Editar</a>
            </div>
        </div>
            </div>
        </div>
        
    </div>
@endsection

@section('footer_includes')
<script src="{{ base_url('public/js/validateForm.min.js') }}"></script>
<script src="{{base_url('public/js/components/PageView.component.min.js')}}"></script>
@endsection