@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/font-awesome/css/all.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection

@section('content')
@include('admin.custommodels.i18n')
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
                <div class="chip" @click="applyPreset('portfolio')"><?= lang('collections_preset_portfolio') ?></div>
                <div class="chip" @click="applyPreset('team')"><?= lang('collections_preset_team') ?></div>
                <div class="chip" @click="applyPreset('faq')"><?= lang('collections_preset_faq') ?></div>
                <div class="chip" @click="applyPreset('cards')"><?= lang('collections_preset_cards') ?></div>
            </div>
            <div class="col s12">
                <div class="input-field col s12 m6">
                    <input v-model="form_name" id="form_name" type="text" class="validate" @input="onNameInput">
                    <label for="form_name" class="active"><?= lang('collections_name') ?></label>
                </div>
                <div class="input-field col s12 m6">
                    <input v-model="slug" id="collection_slug" type="text" class="validate" @input="slugDirty = true">
                    <label for="collection_slug" class="active"><?= lang('collections_slug') ?></label>
                    <span class="helper-text"><?= lang('collections_slug_help') ?></span>
                </div>
                <div class="input-field col s12">
                    <input v-model="form_description" id="form_description" type="text" class="validate">
                    <label for="form_description" class="active"><?= lang('collections_description') ?></label>
                </div>
                <div class="input-field col s12 m6">
                    <select v-model="template" id="collection_template">
                        <option v-for="tpl in templates" :key="tpl" :value="tpl">@{{ tpl }}</option>
                    </select>
                    <label><?= lang('collections_template') ?></label>
                </div>
                <div class="input-field col s12 m6" v-if="apiFieldIds.length">
                    <select v-model="title_field" id="collection_title_field">
                        <option value=""><?= lang('select') ?></option>
                        <option v-for="api in apiFieldIds" :key="api" :value="api">@{{ api }}</option>
                    </select>
                    <label><?= lang('collections_title_field') ?></label>
                </div>
                <div class="col s12" v-if="slug">
                    <label><?= lang('collections_snippet') ?></label>
                    <div class="input-field">
                        <input id="collection_snippet" type="text" readonly :value="snippetText">
                        <a class="btn-flat" href="#!" @click.prevent="copySnippet"><?= lang('collections_copy_snippet') ?></a>
                    </div>
                    <span class="helper-text"><?= lang('collections_snippet_help') ?></span>
                </div>
            </div>
            <div class="col s9">
                <div class="row">
                    <div class="col s12">
                        <ul class="vtabs">
                            <li class="vtab col s3" v-for="(tab, index) in tabs" :id="index"
                                :class="{active : tab.active}">
                                <a :href="'#' + tab.tabID" @click="setActive(index)"
                                    v-if="!tab.edited">@{{tab.tab_name}}</a>
                                <i class="material-icons right" v-if="!tab.edited && index != 0"
                                    @click="deleteTab(index)">delete</i>
                                <input type="text" :id="'input' + index" v-model="tab.tab_name"
                                    v-on:keyup.enter="saveTab(index)" v-on:blur="saveTab(index)" v-if="tab.edited">
                            </li>
                            <li class="vtab col s3"><a href="#tab1" @click="addTab()"><?= lang('collections_new_tab') ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col s12 tab-pane" v-for="(tab, i) in tabs" :id="tab.tabID" :class="{active : tab.active}">
                    <div id="simple-list">
                        <div class="row" v-for="(field, index) in tab.custom_model_fields">
                            <div class="col s12 component">
                                <a class="waves-effect waves-light btn right red darken-2"
                                    @click="removeField(i, index)"><i class="material-icons">delete</i></a>
                                <br>
                                <component :serve-data="field.data" :is="field.component" :tab-parent="tab"
                                    :field-ref-index="index" :field-ref=" field" :configurable="true"
                                    ref="field.component">
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
                                <h5><?= lang('collections_fields') ?></h5>
                            </li>
                            <li class="collection-item" v-for="(formsElement, index) in formsElements">
                                <div>@{{formsElement.displayName}}
                                    <a href="#!" class="secondary-content" @click="addField(formsElement)"><i
                                            class="material-icons">@{{formsElement.icon}}</i></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <?= lang('collections_status') ?>
                <div class="switch">
                    <label>
                        <?= lang('collections_disabled') ?>
                        <input type="checkbox" v-model="status" name="status" value="on">
                        <span class="lever"></span>
                        <?= lang('collections_enabled') ?>
                    </label>
                </div>
            </div>
            <br>
            <div class="col s12 text-center form-group" id="buttons">
                <a href="<?php echo base_url('admin/custommodels/'); ?>"
                    class="btn waves-effect waves-teal btn-flat"><?php echo lang('cancel'); ?></a>
                <button type="button" class="waves-effect waves-light btn" @click="saveData()">
                    <i class="material-icons left">cloud</i> <?= lang('collections_save') ?>
                </button>
            </div>
        </div>
    </div>
</div>
@include('admin.custommodels.forms_fields')
@isset($custom_model_id)
<script>
const custom_model_id = <?php echo json_encode($custom_model_id); ?>;
</script>
@endisset
@endsection

@section('footer_includes')
<script src="{{base_url('public/vendors/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldTitle.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldBoolean.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldNumber.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldDate.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldTime.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldSelect.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formFieldTextArea.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formTextFormat.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/formComponents/formImageSelector.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/CustomModelModule.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
