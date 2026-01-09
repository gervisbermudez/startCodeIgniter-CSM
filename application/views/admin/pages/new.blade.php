@extends('admin.layouts.app') @section('title', $title)
@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/vendors/font-awesome/css/all.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/css/admin/page-new.min.css')?>" />
<link rel="stylesheet" href="<?=base_url('public/vendors/trumbowyg/ui/trumbowyg.min.css')?>" />
<script src="https://unpkg.com/moment@2.22.1/min/moment.min.js"></script>
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
                        <i class="material-icons">assignment</i><span>{{ lang('pages_basic_data') }} </span>
                    </a>
                </li>
                <li class="tab col s3">
                    <a href="#test2" class="active grey-text text-darken-2">
                        <i class="material-icons">build</i>
                        <span>{{ lang('pages_additional') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col s12" id="test1">
            <div v-cloak v-show="!loader" class="col s12 m8 l9">
                <div class="input-field">
                    <label for="title">{{ lang('pages_title') }}</label>
                    <input type="text" id="title" name="title" required="required" value=""
                        v-model="form.fields.title.value" v-on:blur="onChangeTitle(form.fields.title.value);" />
                </div>
                <div class="input-field">
                    <label for="subtitle">{{ lang('pages_subtitle') }}</label>
                    <input type="text" id="subtitle" name="subtitle" required="required" value=""
                        v-model="form.fields.subtitle.value" />
                </div>
                <div class="input-field">
                    <label for="path">{{ lang('pages_path') }}</label>
                    <input type="text" id="path" name="path" required="required" value="" v-model="path" />
                    <br />
                </div>
                <p>
                    <span><b>{{ lang('pages_full_path') }}</b> @{{ getPagePath }}</span>
                </p>
                <a href="#fileUploader" class="waves-effect waves-light btn modal-trigger"
                    @click="setModalMode('copyCallcack')"><i class="material-icons left">add_a_photo</i>{{ lang('pages_add_image') }}</a>
                <div class="row" v-if="mainImage">
                    <div class="col s6" v-for="(image, index) in mainImage" :key="index">
                        <div class="card mainPageImage">
                            <div class="card-image">
                                <i class="material-icons right close tooltipped" data-position="left" data-delay="50"
                                    :data-tooltip="lang('pages_remove_image')" v-on:click="removeImage(index);">close</i>
                                <img class="materialboxed" :src="getFileImagenPath(image)" />
                            </div>
                            <span class="card-title truncate"><span>@{{ getFileImagenName(image) }}</span></span>
                        </div>
                    </div>
                </div>
                <div id="introduction" class="section scrollspy">
                    <span class="header grey-text text-darken-2">{{ lang('pages_content') }} <i
                            class="material-icons left">description</i></span>
                    <br />
                    <textarea v-model="content" id="editor"></textarea>
                    <br />
                </div>
                <br />
            </div>
            <div v-cloak v-show="!loader" class="col s12 m4 l3">
                <span class="header grey-text text-darken-2">{{ lang('pages_publish') }}
                    <i class="material-icons left">assignment_turned_in</i></span>
                <div class="input-field">
                    <div class="switch">
                        <label>
                            {{ lang('pages_not_published') }}
                            <input type="checkbox" name="status_form" value="0" v-model="status" />
                            <span class="lever"></span>
                            {{ lang('pages_published') }}
                        </label>
                    </div>
                </div>
                <div v-show="status">
                    <b class="grey-text text-darken-2"><i class="material-icons left">remove_red_eye</i> {{ lang('pages_visibility') }}</b>
                    <p>
                        <label>
                            <input name="visibility" v-model="visibility" value="1" type="radio" checked />
                            <span>{{ lang('pages_visibility_public') }}</span>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input class="red" name="visibility" v-model="visibility" value="2" type="radio" />
                            <span>{{ lang('pages_visibility_private') }}</span>
                        </label>
                    </p>
                    <b class="grey-text text-darken-2"><i class="material-icons left">date_range</i> {{ lang('pages_publish_date') }}</b>
                    <p>
                        <label>
                            <input type="checkbox" checked v-model="publishondate" />
                            <span>{{ lang('pages_publish_immediately') }}</span>
                        </label>
                    </p>
                    <div v-show="!publishondate">
                        <b class="grey-text text-darken-2">{{ lang('pages_select_date_time') }}</b>
                        <br />
                        <div class="input-field">
                            <label for="datepublish">{{ lang('pages_date') }}</label>
                            <input type="text" class="datepicker" id="datepublish" name="datepublish"
                                required="required" value="" v-model="datepublish" />
                        </div>
                        <div class="input-field">
                            <label for="timepublish">{{ lang('pages_time') }}</label>
                            <input type="text" class="timepicker" id="timepublish" name="timepublish"
                                required="required" value="" v-model="timepublish" />
                        </div>
                    </div>
                </div>
                <br />
                <span class="header grey-text text-darken-2"><i class="material-icons left">list</i> {{ lang('pages_type') }}</span>
                <br />
                <div class="input-field">
                    <select v-model="page_type_id" name="template" @blur="setPath();">
                        <option v-for="(item, index) in pageTypes" :key="index" :value="item.page_type_id">
                            @{{ item.page_type_name | capitalize }}
                        </option>
                    </select>
                    <label>{{ lang('pages_type') }}</label>
                </div>
                <br />
                <span class="header grey-text text-darken-2"><i class="material-icons left">toc</i> {{ lang('events_category') }}</span>
                <br />
                <div class="input-field">
                    <select v-model="categorie_id" id="categorie" name="template" v-on:change="getSubCategories">
                        <option value="0">{{ lang('categories_none') }}</option>
                        <option v-for="(item, index) in categories" :key="index" :value="item.categorie_id">
                            @{{ item.name | capitalize }}
                        </option>
                    </select>
                    <label>{{ lang('events_category') }}</label>
                </div>
                <br />
                <div class="input-field">
                    <select v-model="subcategorie_id" id="subcategories" name="subcategories">
                        <option value="0">{{ lang('categories_none') }}</option>
                        <option v-for="(item, index) in subcategories" :key="index" :value="item.categorie_id">
                            @{{ item.name | capitalize }}
                        </option>
                    </select>
                    <label>{{ lang('pages_subcategory') }}</label>
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
                    <label>{{ lang('pages_template') }}</label>
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
                    <p><b>{{ lang('categories_created_by') }}</b>:</p>
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
                <div class="row">
                    <a v-if="!status" class="btn bg-color3" target="_blank" :href="preview_link"
                        :class="{disabled: !btnEnable}">
                        <span><i class="material-icons right">launch</i> Preview</span>
                    </a>
                    <a v-else class="btn bg-color3" target="_blank" :href="full_path" :class="{disabled: !btnEnable}">
                        <span><i class="material-icons right">launch</i> {{ lang('pages_view_in_site') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div id="test2" class="col s12">
            <div class="col s12 m8 l9" v-cloak v-show="!loader">
                <br />
                <span class="header grey-text text-darken-2">{{ lang('pages_add_tags') }}</span>
                <div class="chips chips-autocomplete" id="pageTags"></div>
            </div>
            <br />
            <div class="row" v-cloak v-show="!loader">
                <div class="input-field col s12">
                    <textarea id="title" v-model="page_data.title" class="materialize-textarea"></textarea>
                    <label for="title">{{ lang('pages_page_title') }}</label>
                </div>
                <div class="input-field col s12">
                    <textarea id="headers_includes" v-model="page_data.headers_includes"
                        class="materialize-textarea"></textarea>
                    <label for="headers_includes">{{ lang('pages_headers_includes') }}</label>
                </div>
                <div class="input-field col s12">
                    <textarea id="footer_includes" v-model="page_data.footer_includes"
                        class="materialize-textarea"></textarea>
                    <label for="footer_includes">{{ lang('pages_footer_includes') }}</label>
                </div>
            </div>
            <div class="row" v-cloak v-show="!loader">
                <div class="col s12">
                    <span class="header grey-text text-darken-2"><i class="material-icons left">layers</i> {{ lang('pages_metas') }} <a
                            class="waves-effect waves-light btn right" href="#!" @click="addCustomMeta();">{{ lang('pages_add_meta') }}
                            +</a></span>
                    <br />
                    <ul class="collapsible" id="pageMetas">
                        <li v-for="(meta, index) in customMetas">
                            <div class="collapsible-header">
                                <i class="material-icons">filter_drama</i>
                                <input placeholder="name" v-model="meta.name" type="text" class="validate">
                                <i class="material-icons right remove"
                                    @click="removeMeta(index, true);">do_not_disturb_on</i>
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
                                <i class="material-icons right remove"
                                    @click="removeMeta(index, false);">do_not_disturb_on</i>
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
                <a href="<?php echo base_url('admin/pages/'); ?>" class="btn-flat"><?php echo lang('cancel'); ?></a>
                <button type="submit" class="btn btn-primary" @click="save" :class="{disabled: !btnEnable}">
                    <span v-if="!status"><i class="material-icons right">edit</i> Guardar Borrador</span>
                    <span v-if="status && btnEnable"><i class="material-icons right">publish</i> Publicar</span>
                    <span v-if="status && !btnEnable"><i class="material-icons right">publish</i> Publicar</span>
                </button>
            </div>
        </div>
    </div>
    <file-explorer-selector :uploader="'single'" :preselected="[]" :modal="'fileUploader'" :mode="'files'"
        :filter="'images'" :multiple="true" v-on:notify="copyCallcack"></file-explorer-selector>
    <file-explorer-selector :uploader="'single'" :preselected="[]" :modal="'editorModal'" :mode="'files'"
        :filter="'images'" :multiple="true" v-on:notify="onSelectImageCallcack"></file-explorer-selector>
</div>
@include('admin.components.file_explorer_selector_component')
@endsection

@section('footer_includes')
<script src="{{base_url('resources/js/validateForm.js')}}"></script>
<script src="{{base_url('public/vendors/trumbowyg/trumbowyg.min.js')}}"></script>
<script src="{{base_url('public/vendors/trumbowyg/plugins/uploadimage/trumbowyg.uploadimage.js')}}"></script>
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<script src="{{base_url('resources/components/PageNewForm.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/plugins/canvas-to-blob.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/locales/es.js')}}"></script>
@endsection