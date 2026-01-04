@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
<div id="root">
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    <nav class="page-navbar" v-cloak v-show="!loader && contents.length > 0">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                                <input class="input-search" type="search" placeholder="<?= lang('custommodels_content_search_placeholder') ?>" v-model="filter">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                <li><a href="#!" v-on:click="getForms();"><i class="material-icons">refresh</i></a></li>
                <li>
                    <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i
                            class="material-icons">more_vert</i></a>
                    <!-- Dropdown Structure -->
                    <ul id='dropdown-options' class='dropdown-content'>
                         <li><a href="#"><?= lang('custommodels_content_archived') ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="pages" v-cloak v-if="!loader && contents.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                                <th><?= lang('custommodels_content_table_type') ?></th>
                                <th><?= lang('custommodels_content_table_description') ?></th>
                                <th><?= lang('custommodels_content_table_author') ?></th>
                                <th><?= lang('custommodels_content_table_status') ?></th>
                                <th><?= lang('custommodels_content_table_publish_date') ?></th>
                                <th><?= lang('custommodels_content_table_options') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(content, index) in filterContents" :key="index">
                            <td>@{{content.custom_model.form_name}}</td>
                            <td>@{{getcontentText(content)}}</td>
                            <td><a
                                    :href="base_url('admin/users/ver/' + content.user_id)">@{{content.user.username}}</a>
                            </td>
                            <td>
                                    <i v-if="content.status == 1" class="material-icons tooltipped" data-position="left"
                                        data-delay="50" :data-tooltip="'<?= lang('custommodels_content_published') ?>'">publish</i>
                                    <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                        :data-tooltip="'<?= lang('custommodels_content_draft') ?>'">edit</i>
                            </td>
                            <td>
                                @{{content.date_publish ? content.date_publish : content.date_create}}
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!'
                                    :data-target='"dropdown_" + content.custom_model_content_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown_" + content.custom_model_content_id' class='dropdown-content'>
                                        <li><a
                                            :href="base_url('admin/custommodels/editData/' + content.custom_model_id + '/' + content.custom_model_content_id)">
                                            <?= lang('custommodels_content_edit') ?></a></li>
                                        <li><a class="modal-trigger" href="#deleteModal"
                                            v-on:click="tempDelete(content, index);"><?= lang('custommodels_content_delete') ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(content, index) in filterContents" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getContentImagePath(content)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown" + content.custom_model_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + content.custom_model_id' class='dropdown-content'>
                                <li><a
                                    :href="base_url('admin/custommodels/editData/' + content.custom_model_id + '/' + content.custom_model_content_id)">
                                    <?= lang('btn_edit') ?></a></li>
                                <li><a class="modal-trigger" href="#deleteModal"
                                    v-on:click="tempDelete(content, index);"><?= lang('btn_delete') ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title">@{{content.custom_model.form_name}} <i
                                        v-if="content.visibility == 1" class="material-icons tooltipped"
                                        data-position="left" data-delay="50" :data-tooltip="'<?= lang('custommodels_content_public') ?>'">public</i>
                                    <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                        :data-tooltip="'<?= lang('custommodels_content_private') ?>'">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(content)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li class="truncate">
                                            <?= lang('custommodels_content_table_author') ?> <a
                                                :href="base_url('admin/users/ver/' + content.user_id)">@{{content.user.username}}</a>
                                        </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{content.form_name}}
                        </span>
                        <span class="subtitle">
                            @{{getcontentText(content)}}
                        </span>
                        <ul>
                            <li><b><?= lang('custommodels_content_table_publish_date') ?></b> <br>
                                @{{content.date_publish ? content.date_publish : content.date_create}}</li>
                                <li><b><?= lang('custommodels_content_table_type') ?></b>@{{content.custom_model.form_name}}</li>
                                <li><b><?= lang('custommodels_content_table_status') ?></b>
                                    <span v-if="content.status == 1">
                                        <?= lang('custommodels_content_published') ?>
                                    </span>
                                    <span v-else>
                                        <?= lang('custommodels_content_draft') ?>
                                    </span>
                                </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && contents.length == 0" v-cloak>
           <h4><?= lang('custommodels_content_no_contents') ?></h4>
    </div>
    <confirm-modal id="deleteModal" title="Confirmar Borrar" v-on:notify="confirmCallback">
        <p>
              <?= lang('custommodels_content_confirm_delete_message') ?>
        </p>
    </confirm-modal>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/FormContentList.js')}}"></script>
@endsection
