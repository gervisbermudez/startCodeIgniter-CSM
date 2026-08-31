@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection
@section('content')
<div class="container form" id="root">
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{$h1}}</h3>
        </div>
    </div>
    <div class="row">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <preloader />
        </div>
        <div id="form" class="col s12" v-bind:class="{'m10': user_id}" v-cloak v-show="!loader">
            <input type="hidden" name="event_id" value="{{$event_id}}">
            <span class="header grey-text text-darken-2">{{ lang('events_happening') }} <i
                    class="material-icons left">event</i></span>
            <div class="input-field">
                <p>
                    <label>
                        <input type="checkbox" v-model="all_day" />
                        <span>{{ lang('events_all_day') }}</span>
                    </label>
                </p>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <label class="active" for="event_date_start">{{ lang('events_start') }}</label>
                    <input type="text" class="datepicker" id="event_date_start" name="event_date_start"
                        v-model="date_start_date" />
                </div>
                <div class="input-field col s12 m6" v-show="!all_day">
                    <label class="active" for="event_time_start">{{ lang('events_start_time') }}</label>
                    <input type="text" class="timepicker" id="event_time_start" name="event_time_start"
                        v-model="date_start_time" />
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <label class="active" for="event_date_end">{{ lang('events_end') }}</label>
                    <input type="text" class="datepicker" id="event_date_end" name="event_date_end"
                        v-model="date_end_date" />
                </div>
                <div class="input-field col s12 m6" v-show="!all_day">
                    <label class="active" for="event_time_end">{{ lang('events_end_time') }}</label>
                    <input type="text" class="timepicker" id="event_time_end" name="event_time_end"
                        v-model="date_end_time" />
                </div>
            </div>
            <div class="input-field">
                <select id="location_type" v-model="location_type">
                    <option value="physical">{{ lang('events_location_physical') }}</option>
                    <option value="online">{{ lang('events_location_online') }}</option>
                    <option value="hybrid">{{ lang('events_location_hybrid') }}</option>
                </select>
                <label for="location_type">{{ lang('events_location') }}</label>
            </div>
            <div class="input-field" v-show="location_type === 'physical' || location_type === 'hybrid'">
                <label class="active" for="address">{{ lang('events_address') }}</label>
                <input type="text" v-model="address" id="address" name="address_form">
            </div>
            <div class="input-field" v-show="location_type === 'online' || location_type === 'hybrid'">
                <label class="active" for="online_url">{{ lang('events_online_url') }}</label>
                <input type="url" v-model="online_url" id="online_url" name="online_url">
            </div>
            <div class="input-field">
                <label class="active" for="slug">{{ lang('events_slug') }}</label>
                <input type="text" v-model="slug" id="slug" name="slug" @input="slugManual = true">
            </div>
            <span class="header grey-text text-darken-2">{{ lang('events_basic_data') }} <i
                    class="material-icons left">description</i></span>
            <div class="input-field">
                <label class="active" for="nombre">{{ lang('events_name') }}</label>
                <input type="text" v-model="name" id="nombre" name="nombre_form" required="required">
            </div>
            <div class="input-field">
                <label class="active" for="subtitle">{{ lang('events_subtitle') }}</label>
                <input type="text" v-model="subtitle" id="subtitle" name="subtitle_form">
            </div>
            <a href="#fileUploader" class="waves-effect waves-light btn modal-trigger"><i
                    class="material-icons left">add_a_photo</i>{{ lang('events_add_image') }}</a>
            <div class="row" v-if="mainImage">
                <div class="col s12">
                    <div class="card mainPageImage" v-for="(image, index) in mainImage" :key="index">
                        <div class="card-image">
                             <i class="material-icons right close tooltipped" data-position="left" data-delay="50"
                                :data-tooltip="lang('events_remove_image')" v-on:click="removeImage(index);">close</i>
                            <img :src="getFileImagenPath(image)" />
                            <span class="card-title">@{{ getFileImagenName(image) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <b class="grey-text text-darken-2"><i class="material-icons left">date_range</i> {{ lang('events_cms_publish') }}</b>
            <p>
                <label>
                    <input type="checkbox" v-model="publishondate" />
                    <span>{{ lang('events_publish_immediately') }}</span>
                </label>
            </p>
            <div v-show="!publishondate">
                <b class="grey-text text-darken-2">{{ lang('events_select_date_time') }}</b>
                <br />
                <div class="input-field">
                    <label class="active" for="datepublish">{{ lang('events_date') }}</label>
                    <input type="text" class="datepicker" id="datepublish" name="datepublish"
                        v-model="datepublish" />
                </div>
                <div class="input-field">
                    <label class="active" for="timepublish">{{ lang('events_time') }}</label>
                    <input type="text" class="timepicker" id="timepublish" name="timepublish"
                        v-model="timepublish" />
                </div>
            </div>
            <div id="introduction" class="section scrollspy">
                <label class="active" for="id_cazary">{{ lang('events_description') }}</label>
                <div class="input-field">
                    <textarea id="id_cazary" v-model="content" name="descripcion_form"></textarea>
                </div>
                <br>
            </div>
            <div class="input-field" v-if="categories.length > 0">
                <br>
                <select v-model="categorie_id">
                    <option value="0">{{ lang('categories_none') }}</option>
                    <option v-for="(item, index) in categories" :key="index" :value="item.categorie_id">
                        @{{item.name | capitalize}}</option>
                </select>
                <label>{{ lang('events_category') }}</label>
                <br>
            </div>
            <br>
            {{ lang('events_visibility') }}
            <br>
            <div class="input-field">
                <div class="switch">
                    <label>
                        {{ lang('events_not_visible') }}
                        <input type="checkbox" name="visible_form" value="1" v-model="visibility">
                        <span class="lever"></span>
                        {{ lang('events_visible') }}
                    </label>
                </div>
            </div>
            <br>
            {{ lang('events_publish') }}
            <br>
            <div class="input-field">
                <div class="switch">
                    <label>
                        {{ lang('events_not_published') }}
                        <input type="checkbox" name="status_form" value="1" v-model="status">
                        <span class="lever"></span>
                        {{ lang('events_published') }}
                    </label>
                </div>
            </div>
            <br><br>
            <div class="input-field" id="buttons">
                <a href="<?php echo base_url('admin/events/'); ?>" class="btn-flat">{{ lang('btn_cancel') }}</a>
                <button type="submit" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
                    <span><i class="material-icons right">edit</i> {{ lang('btn_save') }}</span>
                </button>
            </div>
        </div>
        <div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id" v-show="!loader">
            <span class="header grey-text text-darken-2">{{ lang('events_additional') }} <i class="material-icons left">description</i></span>
            <p>
                <b>{{ lang('events_created_by') }}</b>:
                <user-info :user="user" />
            </p>
            <p>
                <b>{{ lang('events_start') }}</b>: <br>
                <span>@{{date_start}}</span> <br><br>
                <b>{{ lang('events_end') }}</b>: <br>
                <span>@{{date_end || '—'}}</span> <br><br>
                <b>{{ lang('events_created') }}</b>: <br>
                <span>@{{date_create}}</span> <br><br>
                <b>{{ lang('events_modified') }}</b>: <br>
                <span>@{{date_update}}</span> <br><br>
                <b>{{ lang('events_cms_publish') }}</b>: <br>
                <span>@{{date_publish}}</span>
            </p>
        </div>
    </div>
    <file-explorer-selector :uploader="'single'" :preselected="[]" :modal="'fileUploader'" :mode="'files'"
        :filter="'images'" :multiple="false" v-on:notify="copyCallcack"></file-explorer-selector>
</div>
@include('admin.components.file_explorer_selector_component')
</div>
<script>
const event_id = <?=json_encode($event_id ? $event_id : false);?>;
const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<script src="{{base_url('public/vendors/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/EventNewForm.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
