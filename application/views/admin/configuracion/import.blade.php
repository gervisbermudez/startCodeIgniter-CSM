@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection
@section('content')
<div class="container form export" id="root">
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{$h1}}</h3>
        </div>
    </div>
    <div class="row">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <preloader />
        </div>
        <div id="form" class="col s12" v-cloak v-show="!loader">
            <div class="row">
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>Seleccionar archivo...</span>
                            <input type="file" id="files" name="files[]" multiple v-on:change="handleFileSelect" />
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <ul class="collapsible" v-show="selectedFile">
                        <li v-if="exportData.pages.length">
                            <div class="toggle-all-data">
                                <label>
                                    <input type="checkbox" v-on:change="toggleData(exportData.pages, 'pages')" />
                                    <span></span>
                                </label>
                            </div>
                            <div class="collapsible-header"><i class="material-icons">web</i>Paginas
                            </div>
                            <div class="collapsible-body">
                                <ul class="collection">
                                    <li class="collection-item avatar" v-for="(page, index) in exportData.pages"
                                        :key="index">
                                        <div class="material-icons circle checkbox">
                                            <label>
                                                <input type="checkbox" :checked="page.checked" v-model="page.checked" />
                                                <span></span>
                                            </label>
                                        </div>
                                        <span class="title"><b>@{{page.title}}</b></span>
                                        <p>@{{page.path}}<br>
                                        </p>
                                        <a :href="base_url('admin/pages/view/' + page.page_id)"
                                            class="secondary-content"><i class="material-icons">play_arrow</i></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li v-if="exportData.config.length">
                            <div class="toggle-all-data">
                                <label>
                                    <input type="checkbox" v-on:change="toggleData(exportData.config, 'config')" />
                                    <span></span>
                                </label>
                            </div>
                            <div class="collapsible-header"><i class="material-icons">settings</i> Configuracion</div>
                            <div class="collapsible-body">
                                <ul class="collection">
                                    <li class="collection-item avatar" v-for="(item, index) in exportData.config"
                                        :key="index">
                                        <div class="material-icons circle checkbox">
                                            <label>
                                                <input type="checkbox" v-model="item.checked" :checked="item.checked" />
                                                <span></span>
                                            </label>
                                        </div>
                                        <span class="title"><b>@{{item.config_label}}</b></span>
                                        <p>@{{item.config_name}}<br>
                                            @{{item.config_value}}<br>
                                            @{{item.config_type}}
                                        </p>
                                        <a :href="base_url('admin/configuration?section=' + item.config_type)"
                                            class="secondary-content"><i class="material-icons">edit</i></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="input-field" id="buttons" v-show="selectedFile">
                <a href="<?php echo base_url('admin/configuration/'); ?>" class="btn-flat">Cancelar</a>
                <button type="submit" class="btn btn-primary" @click="saveData()" :class="{disabled: !btnEnable}">
                    <span><i class="material-icons right">save</i> Import</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/import.component.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection