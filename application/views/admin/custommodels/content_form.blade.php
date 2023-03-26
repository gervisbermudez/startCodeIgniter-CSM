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
    <preloader />
    </div>
    <div v-cloak v-show="!loader">
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12">
                        <ul class="vtabs">
                            <li class="vtab col s3" v-for="(tab, index) in tabs" :id="index" :class="{active : tab.active}">
                                <a :href="'#' + tab.tabID" @click="setActive(index)" v-if="!tab.edited">@{{tab.tab_name}}</a>
                                <input type="text" :id="'input' + index" v-model="tab.tab_name" v-on:keyup.enter="saveTab(index)" v-on:blur="saveTab(index)" v-if="tab.edited">
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col s12 tab-pane" v-for="(tab, i) in tabs" :id="tab.tabID" :class="{active : tab.active}">
                    <div id="simple-list">
                        <div class="row" v-for="(field, index) in tab.form_fields">
                            <div class="col s12 component">
                                <component :serve-data="field.data" :field-data="field.field_data" :is="field.component" :tab-parent="tab" :field-ref-index="index" :field-ref="field" :configurable="false" ref="field.component">
                                </component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                Publicar Data
                <div class="switch">
                    <label>
                        No Publicado
                        <input type="checkbox" checked v-model="status" name="status" value="on">
                        <span class="lever"></span>
                        Publicado
                    </label>
                </div>
            </div>
            <br>
            <div class="col s12 text-center form-group" class="" id="buttons">
                <a href="<?php echo base_url('admin/custommodels/'); ?>" class="btn waves-effect waves-teal btn-flat">Cancelar</a>
                <a class="waves-effect waves-light btn waves-effect waves-teal" @click="saveData()"><i class="material-icons left">cloud</i> Guardar</a>
            </div>
        </div>
    </div>
</div>
@include('admin.custommodels.formsFields')
@isset($form_content_id)
<script>
    const form_content_id = <?=json_encode($form_content_id);?>;
    const form_custom_id = <?=json_encode($form_custom_id);?>;
</script>
@endisset
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/fileinput-master/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/js/fileinput-master/js/plugins/canvas-to-blob.min.js')}}"></script>
<script src="{{base_url('public/js/fileinput-master/js/locales/es.js')}}"></script>
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('public/js/components/FileExplorerSelector.min.js')}}"></script>
<script src="{{base_url('public/js/components/FormContentNewModuleBundle.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
