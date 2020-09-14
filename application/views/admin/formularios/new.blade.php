@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/fileinput-master/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection

@section('content')
<div class="container formModule form" id="root">
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{$h1}}</h3>
        </div>
    </div>
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
    <div v-cloak v-show="!loader">
        <div class="row">
            <div class="col s12">
                <div class="input-field col s12">
                    <input placeholder="Form Name" v-model="form_name" id="form_name" type="text" class="validate">
                    <label for="form_name" class="active">Form Name</label>
                </div>
                <div class="input-field col s12">
                    <input placeholder="Form Name" v-model="form_description" id="form_description" type="text" class="validate">
                    <label for="form_description" class="active">Form Desription</label>
                </div>
            </div>
            <div class="col s9">
                <div class="row">
                    <div class="col s12">
                        <ul class="vtabs">
                            <li class="vtab col s3" v-for="(tab, index) in tabs" :id="index" :class="{active : tab.active}">
                                <a :href="'#' + tab.tabID" @click="setActive(index)" v-if="!tab.edited">@{{tab.tab_name}}</a>
                                <i class="material-icons right" v-if="!tab.edited && index != 0" @click="deleteTab(index)">delete</i>
                                <input type="text" :id="'input' + index" v-model="tab.tab_name" v-on:keyup.enter="saveTab(index)" v-on:blur="saveTab(index)" v-if="tab.edited">
                            </li>
                            <li class="vtab col s3"><a href="#tab1" @click="addTab()">New Tab +</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col s12 tab-pane" v-for="(tab, i) in tabs" :id="tab.tabID" :class="{active : tab.active}">
                    <div id="simple-list">
                        <div class="row" v-for="(field, index) in tab.form_fields">
                            <div class="col s12 component">
                                <a class="waves-effect waves-light btn right red darken-2 tooltipped" data-position="left" data-tooltip="Remove Field" @click="removeField(i, index)"><i class="material-icons">delete</i></a>
                                <br>
                                <component :serve-data="field.data" :is="field.component" :tab-parent="tab" :field-ref-index="index" :field-ref=" field" :configurable="true" ref="field.component">
                                </component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m3 formsElements">
                <div class="row">
                    <div class="col s12">
                        <ul class="collection with-header">
                            <li class="collection-header">
                                <h5>Campos</h5>
                            </li>
                            <li class="collection-item" v-for="(formsElement, index) in formsElements">
                                <div>@{{formsElement.displayName}}
                                    <a href="#!" class="secondary-content" @click="addField(formsElement)"><i class="material-icons">@{{formsElement.icon}}</i></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                Activar Formulario
                <div class="switch">
                    <label>
                        No activo
                        <input type="checkbox" checked v-model="status" name="status" value="on">
                        <span class="lever"></span>
                        Activo
                    </label>
                </div>
            </div>
            <br>
            <div class="col s12 text-center form-group" class="" id="buttons">
                <a href="<?php echo base_url('admin/formularios/'); ?>" class="btn waves-effect waves-teal btn-flat">Cancelar</a>
                <a class="waves-effect waves-light btn waves-effect waves-teal" @click="saveData()"><i class="material-icons left">cloud</i> Guardar</a>
            </div>
        </div>
    </div>
</div>
@include('admin.formularios.formsFields')
@isset($form_custom_id)
<script>
    const form_custom_id = < ? = json_encode($form_custom_id); ? > ;

</script>
@endisset
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('public/js/components/FormNewModuleBundle.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
