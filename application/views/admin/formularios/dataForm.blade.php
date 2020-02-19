@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container" id="dataFormModule">
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
    <div v-cloak v-if="!loader">
        <div class="row">
            <div class="col s12">
                <div class="col s12">
                    <h2>
                        @{{getFormName}}
                    </h2>
                </div>
            </div>
            <div class="col s12">
                <div class="row">
                    <div class="col s12">
                        <ul class="vtabs">
                            <li class="vtab col s3" v-for="(tab, index) in tabs" :id="index"
                                :class="{active : tab.active}">
                                <a :href="'#' + tab.tabID" @click="setActive(index)"
                                    v-if="!tab.edited">@{{tab.name}}</a>
                                <i class="material-icons right" v-if="!tab.edited && index != 0"
                                    @click="deleteTab(index)">delete</i>
                                <input type="text" :id="'input' + index" v-model="tab.name"
                                    v-on:keyup.enter="saveTab(index)" v-on:blur="saveTab(index)" v-if="tab.edited">
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col s12 tab-pane" v-for="(tab, i) in tabs" :id="tab.tabID" :class="{active : tab.active}">
                    <div id="simple-list">
                        <div class="row" v-for="(field, index) in tab.fields">
                            <div class="col s12 component">
                                <component :serve-data="field.serveData" :is="field.component" :tab-parent="tab"
                                    :field-ref-index="index" :field-ref="field" ref="field.component">
                                </component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <br>
            <div class="col s12 text-center form-group" class="" id="buttons">
                <a href="<?php echo base_url('admin/formularios/'); ?>" class="btn red darken-1">Cancelar</a>
                <a class="waves-effect waves-light btn" @click="saveData()"><i class="material-icons right">cloud</i>
                    Guardar</a>
            </div>
        </div>
    </div>
</div>
<script type="text/x-template" id="formFieldTitle-template">
    <div class="row formFieldTitle">
        <div class="input-field col s12"> 
            <input :placeholder="fieldPlaceholder" v-model="data.fieldValue" @keyup="convertfielApiID()" :id="fieldID" type="text" class="validate">
            <label :for="fieldID" class="active">@{{fieldName}}</label>
        </div>
    </div>
</script>
@isset($form_id)
<script>
    const form_id = <?= json_encode($form_id); ?>;
    const editMode = <?= json_encode($editMode); ?>;
    const form_content_id = <?= json_encode($form_content_id); ?>;
</script>
@endisset
@endsection