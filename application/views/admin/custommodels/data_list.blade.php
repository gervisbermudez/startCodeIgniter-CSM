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
    @include('admin.components.page_navbar', [
        'searchInputId' => 'custommodels-data-search',
        'refreshMethod' => 'getModels()',
        'navbarShow' => '!loader && models.length > 0',
        'itemsExpr' => 'filterModels',
    ])
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
                                    @if(has_permisions('CREATE_CONTENT_DATA'))
                                    <li><a :href="base_url('admin/custommodels/addData/' + model.custom_model_id)"> <?= lang('custommodels_add_data_data') ?></a></li>
                                    @endif
                                    @if(has_permisions('UPDATE_FORM_CUSTOM'))
                                    <li><a :href="base_url('admin/custommodels/editForm/' + model.custom_model_id)"> <?= lang('custommodels_edit_data') ?></a></li>
                                    @endif
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
                            @if(has_permisions('CREATE_CONTENT_DATA'))
                            <li><a :href="base_url('admin/custommodels/addData/' + model.custom_model_id)"> <?= lang('custommodels_add_data_data') ?></a></li>
                            @endif
                            @if(has_permisions('UPDATE_FORM_CUSTOM'))
                            <li><a :href="base_url('admin/custommodels/editForm/' + model.custom_model_id)"> <?= lang('custommodels_edit_data') ?></a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(model.path)" target="_blank">@{{model.form_name}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'model'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{model.form_description}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info v-if="model.user" :user="model.user" />
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
@if(has_permisions('CREATE_FORM_CUSTOM'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="<?= lang('custommodels_new_form_tooltip') ?>" href="{{base_url('admin/custommodels/nuevo')}}">
        <i class="material-icons">add</i>
    </a>
</div>
@endif
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/formComponents/formFieldTitle.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/DataFormModule.js')}}"></script>
@endsection
