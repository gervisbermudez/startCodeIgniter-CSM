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
            @isset($collection_name)
            <p>{{ $collection_name }}</p>
            @endisset
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
                        <div class="row" v-for="(field, index) in tab.custom_model_fields">
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
            <div class="input-field col s12 m4">
                <label>
                    <input type="checkbox" class="filled-in" v-model="featured">
                    <span><?= lang('collections_featured') ?></span>
                </label>
            </div>
            <div class="input-field col s12 m4">
                <input id="sort_order" type="number" v-model.number="sort_order">
                <label for="sort_order" class="active"><?= lang('collections_sort_order') ?></label>
            </div>
            <div class="form-group col s12 m4">
                <?= lang('custommodels_content_table_status') ?>
                <div class="switch">
                    <label>
                        <?= lang('custommodels_content_draft') ?>
                        <input type="checkbox" v-model="status" name="status" value="on">
                        <span class="lever"></span>
                        <?= lang('custommodels_content_published') ?>
                    </label>
                </div>
            </div>
            <br>
            <div class="col s12 text-center form-group" id="buttons">
                <a :href="base_url('admin/custommodels/items/' + custom_model_id)" class="btn waves-effect waves-teal btn-flat"><?= lang('btn_cancel') ?></a>
                <button type="button" class="waves-effect waves-light btn" @click="saveData()">
                    <i class="material-icons left">cloud</i> <?= lang('btn_save') ?>
                </button>
            </div>
        </div>
    </div>
</div>
@include('admin.custommodels.forms_fields')
@isset($custom_model_content_id)
<script>
    const custom_model_content_id = <?=json_encode($custom_model_content_id);?>;
    const custom_model_id = <?=json_encode($custom_model_id);?>;
</script>
@endisset
@endsection

@section('footer_includes')
<script src="{{base_url('public/vendors/fileinput/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/plugins/canvas-to-blob.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/locales/es.js')}}"></script>
<script src="{{base_url('public/vendors/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<!-- Load form field components before CustomModelContentModule -->
<script src="{{base_url('public/js/form-fields.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/CustomModelContentModule.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
