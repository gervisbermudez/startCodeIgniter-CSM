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
    <nav class="page-navbar" v-cloak v-show="!loader && models.length > 0">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                    <input class="input-search" type="search" placeholder="<?= lang('custommodels_search_placeholder_data') ?>" v-model="filter">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                <li><a href="#!" v-on:click="getModels();"><i class="material-icons">refresh</i></a></li>
                <li>
                    <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i class="material-icons">more_vert</i></a>
                    <!-- Dropdown Structure -->
                    <ul id='dropdown-options' class='dropdown-content'>
                        <li><a href="#!">one</a></li>
                        <li><a href="#!">two</a></li>
                        <li class="divider" tabindex="-1"></li>
                        <li><a href="#!">three</a></li>
                        <li><a href="#!"><i class="material-icons">view_module</i>four</a></li>
                        <li><a href="#!"><i class="material-icons">cloud</i>five</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="pages" v-cloak v-if="!loader && models.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th><?= lang('custommodels_table_name_data') ?></th>
                            <th><?= lang('custommodels_table_description_data') ?></th>
                            <th><?= lang('custommodels_table_author_data') ?></th>
                            <th><?= lang('custommodels_table_status_data') ?></th>
                            <th><?= lang('custommodels_table_publish_date_data') ?></th>
                            <th><?= lang('custommodels_table_options_data') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(model, index) in filterModels" :key="index">
                            <td>@{{model.form_name}}</td>
                            <td>@{{model.form_description}}</td>
                            <td><a :href="base_url('admin/users/ver/' + model.user_id)">@{{model.user.username}}</a></td>
                            <td>
                                <i v-if="model.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="'<?= lang('custommodels_published_data') ?>'">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="'<?= lang('custommodels_draft_data') ?>'">edit</i>
                            </td>
                            <td>
                                @{{model.date_publish ? model.date_publish : model.date_create}}
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + model.custom_model_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + model.custom_model_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/custommodels/addData/' + model.custom_model_id)"> <?= lang('custommodels_add_data_data') ?></a></li>
                                    <li><a :href="base_url('admin/custommodels/editForm/' + model.custom_model_id)"> <?= lang('custommodels_edit_data') ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(model, index) in filterModels" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(model)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + model.custom_model_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + model.custom_model_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/custommodels/addData/' + model.custom_model_id)"> <?= lang('custommodels_add_data_data') ?></a></li>
                            <li><a :href="base_url('admin/custommodels/editForm/' + model.custom_model_id)"> <?= lang('custommodels_edit_data') ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(model.path)" target="_blank">@{{model.form_name}}</a> <i v-if="model.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="'<?= lang('custommodels_public') ?>'">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="'<?= lang('custommodels_private') ?>'">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{model.form_description}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li class="truncate">
                                        <?= lang('custommodels_author') ?> <a :href="base_url('admin/users/ver/' + model.user_id)">@{{model.user.username}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{model.form_name}}
                        </span>
                        <span class="subtitle">
                            @{{model.form_description}}
                        </span>
                        <ul>
                            <li><b><?= lang('custommodels_publish_date') ?></b> <br> @{{model.date_publish ? model.date_publish : model.date_create}}</li>
                            <li><b><?= lang('custommodels_category') ?></b> @{{model.categorie}}</li>
                            <li><b><?= lang('custommodels_subcategory') ?></b> @{{model.subcategorie ? model.subcategorie : lang('custommodels_none')}}</li>
                            <li><b><?= lang('custommodels_template') ?></b> @{{model.template}}</li>
                            <li><b><?= lang('custommodels_type') ?></b> @{{model.page_type_name}}</li>
                            <li><b><?= lang('custommodels_status') ?></b>
                                <span v-if="model.status == 1">
                                    <?= lang('custommodels_published_data') ?>
                                </span>
                                <span v-else>
                                    <?= lang('custommodels_draft_data') ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && models.length == 0" v-cloak>
        <h4><?= lang('custommodels_no_forms') ?></h4>
    </div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="<?= lang('custommodels_new_form_tooltip') ?>" href="{{base_url('admin/custommodels/nuevo')}}">
        <i class="material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/DataFormModule.js')}}"></script>
@endsection
