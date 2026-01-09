@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/userprofile.min.css')?>">
@endsection

@section('content')
<div id="root">
    <div class="row">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <preloader />
        </div>
        <div class="col s12 m5 l4" v-cloak v-show="!loader">
            <div class="card banner">
                <!-- Dropdown Trigger -->
                <a class='dropdown-trigger right' href='#' data-target='<?=$dropdown_id?>'>
                    <i class="material-icons">more_vert</i></a>
                <?=$dropdown_menu?>
                <div class="card-image">
                    <img src="<?=base_url('public/img/profile/usertop.jpg');?>">
                </div>
                <div class="avatar">
                    <a href="#folderSelector" class="modal-trigger" v-if="user.user_data.avatar">
                        <img :src="user. get_avatarurl()" :alt="user.username" class="circle z-depth-1">
                    </a>
                    <a href="#folderSelector" class="modal-trigger" v-else>
                        <i class="material-icons circle grey lighten-5 z-depth-1">account_circle</i></a>
                </div>
                <div class="card-content row">
                    <div class="col s12 center-align">
                        <span class="card-title">@{{user.get_fullname()}}</span>
                        <p>
                            @{{user.role}}
                        </p>
                    </div>
                    <div class="col s12 center-align">
                        <span class="card-title">@{{user.username}}</span>
                        <p>
                            @{{timeAgo(user.lastseen)}}
                        </p>
                    </div>
                </div>
            </div>
            <ul class="collection with-header">
                <li class="collection-header">
                    <span><?php echo lang('user_data'); ?></span>
                </li>
                <li class="collection-item"><i class="material-icons left">message</i>
                    <a href="#!"><?=$user->email?></a>
                </li>
                <li class="collection-item"><i class="material-icons left">contact_phone</i>
                    <a href="#!"><?=$user->user_data->telefono?></a>
                </li>
                <li class="collection-item"><i class="material-icons left">location_on</i>
                    <a href="#!"><?=$user->user_data->direccion?></a>
                </li>
            </ul>
        </div>
        <div class="col s12 m7 l8 timeline-content" v-cloak v-show="!loader">
            <div class="row">
                <div class="col s12">
                    <ul class="tabs" id="user-tabs">
                        <li class="tab col s12 m4"><a class="active" href="#test1"> <i class="material-icons">view_day</i> <span><?php echo lang('activity'); ?></span></a></li>
                    </ul>
                </div>
                <div id="test1" class="col s12">
                    <ul class="timeline">
                        <!-- timeline time label -->
                        <template v-for="(item, index) in timelineData">
                            <li class="time-label">
                                <span class="bg-red">
                                    @{{timeAgo(index) | shortDate}}
                                </span>
                            </li>
                            <!-- /.timeline-label -->
                            <!-- timeline item -->
                            <li v-for="(element, i) in item" :key="makeid(5)">
                                <template v-if="element.model_type == 'page'">
                                    <i class="material-icons bg-blue mark">web</i>
                                    <div class="timeline-item">
                                        <div class="card">
                                            <div class="card-header">
                                                <user-info :user="element.user"></user-info>
                                                <a class="waves-effect waves-light dropdown-trigger" href='#!'
                                                :data-target='"page_id" + element.page_id'>
                                                <i class="material-icons">more_vert</i></a>
                                                <ul :id='"page_id" + element.page_id' class='dropdown-content'>
                                                    <li><a :href="base_url('admin/pages/editar/' + element.page_id)"><?php echo lang('edit'); ?></a></li>
                                                    <li ><a :href="base_url('admin/pages/view/' + element.page_id)">Preview</a></li>
                                                </ul>

                                            </div>
                                            <div class="card-content">
                                                <span class="card-title">@{{element.title}}</span>
                                                <div class="card-image-container">
                                                    <img :src="getPageImagePath(element)" />
                                                </div>
                                                <p>
                                                    @{{getcontentText(element)}}
                                                </p>                                            </div>
                                            <div class="card-action">
                                            <a :href="base_url('admin/pages/editar/' + element.page_id)"><?php echo lang('edit'); ?></a>
                                            <a :href="base_url('admin/pages/view/' + element.page_id)">Preview</a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-if="element.model_type == 'custom_model'">
                                    <i class="material-icons bg-yellow mark">insert_chart</i>
                                    <div class="timeline-item">
                                    <div class="card">
                                            <div class="card-header">
                                                <user-info :user="element.user"></user-info>
                                                <a class="dropdown-trigger right" href='#!' :data-target='"custom_model_id" + element.custom_model_id'> <i class="material-icons">more_vert</i></a>
                                                <ul :id='"custom_model_id" + element.custom_model_id' class='dropdown-content'>
                                                    <li><a :href="base_url('admin/custommodels/addData/' + element.custom_model_id)"> Add data</a></li>
                                                    <li><a :href="base_url('admin/custommodels/editForm/' + element.custom_model_id)"> Edit</a></li>
                                                </ul>
                                            </div>
                                            <div class="card-content">
                                                <span class="card-title">@{{element.form_name}}</span>
                                                <p>
                                                    @{{element.form_description}}
                                                </p>                                            </div>
                                            <div class="card-action">
                                                <a :href="base_url('admin/CustomModels/addData/' + element.custom_model_id)">Add content</a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </li>
                        </template>
                        <li>
                            <i class="material-icons mark bg-gray">access_time</i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <file-explorer-selector
    :uploader="'single'"
    :preselected="[]"
    :modal="'folderSelector'"
    :mode="'files'"
    :filter="'images'"
    :multiple="true"
    v-on:notify="uploadCallback"
    :initialdir="'./public/img/profile/' + user.username + '/'"
    ></file-explorer-selector>
</div>
@include('admin.components.file_explorer_selector_component')
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/FileExplorerSelector.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/fileinput.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/plugins/canvas-to-blob.min.js')}}"></script>
<script src="{{base_url('public/vendors/fileinput/js/locales/es.js')}}"></script>
<script src="<?=base_url('resources/components/UserProfileComponent.js');?>"></script>
@endsection
