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
    <nav class="page-navbar" v-cloak v-show="!loader && forms.length > 0">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                    <input class="input-search" type="search" placeholder="Buscar..." v-model="filter">
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
                                <li><a href="#!"><?= lang('custommodels_archived') ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="pages" v-cloak v-if="!loader && forms.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th><?= lang('custommodels_table_name') ?></th>
                            <th><?= lang('custommodels_table_description') ?></th>
                            <th><?= lang('custommodels_table_author') ?></th>
                            <th><?= lang('custommodels_table_status') ?></th>
                            <th><?= lang('custommodels_table_publish_date') ?></th>
                            <th><?= lang('custommodels_table_options') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(form, index) in filterForms" :key="index">
                            <td>@{{form.form_name}}</td>
                            <td>@{{getcontentText(form.form_description)}}</td>
                            <td>
                                <a v-if="form.user" :href="base_url('admin/users/ver/' + form.user_id)">@{{form.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{form.date_publish ? form.date_publish : form.date_create}}
                            </td>
                            <td>
                                <i v-if="form.status == 1" class="material-icons tooltipped" data-position="left"
                                    data-delay="50" :data-tooltip="'<?= lang('custommodels_published') ?>'">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    :data-tooltip="'<?= lang('custommodels_draft') ?>'">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!'
                                    :data-target='"dropdown_" + form.custom_model_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown_" + form.custom_model_id' class='dropdown-content'>
                                        <li><a :href="base_url('admin/custommodels/addData/' + form.custom_model_id)">
                                            <?= lang('custommodels_add_data') ?></a></li>
                                        <li><a :href="base_url('admin/custommodels/editForm/' + form.custom_model_id)">
                                            <?= lang('custommodels_edit') ?></a></li>
                                        <li><a class="modal-trigger" href="#deleteModal"
                                            v-on:click="tempDelete(form, index);"><?= lang('custommodels_delete') ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(form, index) in filterForms" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(form)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown" + form.custom_model_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + form.custom_model_id' class='dropdown-content'>
                                <li><a :href="base_url('admin/custommodels/addData/' + form.custom_model_id)"> <?= lang('custommodels_add_data') ?></a></li>
                                <li><a :href="base_url('admin/custommodels/editForm/' + form.custom_model_id)"> <?= lang('custommodels_edit') ?></a></li>
                                <li><a class="modal-trigger" href="#deleteModal"
                                    v-on:click="tempDelete(form, index);"><?= lang('custommodels_delete') ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(form.path)"
                                    target="_blank">@{{form.form_name}}</a> <i v-if="form.visibility == 1"
                                    class="material-icons tooltipped" data-position="left" data-delay="50"
                                    :data-tooltip="'<?= lang('custommodels_published') ?>'">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    :data-tooltip="'<?= lang('custommodels_draft') ?>'">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(form.form_description)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li class="truncate" v-if="form.user">
                                        <?= lang('custommodels_author') ?> <a
                                            :href="base_url('admin/users/ver/' + form.user_id)">@{{form.user.get_fullname()}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{form.form_name}}
                        </span>
                        <span class="subtitle">
                            @{{form.form_description}}
                        </span>
                        <ul>
                            <li><b><?= lang('custommodels_publish_date') ?>:</b> <br>
                                @{{form.date_publish ? form.date_publish : form.date_create}}</li>
                            <li><b><?= lang('custommodels_category') ?></b> @{{form.categorie}}</li>
                            <li><b><?= lang('custommodels_subcategory') ?></b> @{{form.subcategorie ? form.subcategorie : '<?= lang('custommodels_none') ?>'}}</li>
                            <li><b><?= lang('custommodels_template') ?></b> @{{form.template}}</li>
                            <li><b><?= lang('custommodels_type') ?></b> @{{form.page_type_name}}</li>
                            <li><b><?= lang('custommodels_status') ?></b>
                                <span v-if="form.status == 1">
                                    <?= lang('custommodels_published') ?>
                                </span>
                                <span v-else>
                                    <?= lang('custommodels_draft') ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <div class="container" v-if="!loader && forms.length == 0" v-cloak>
        <h4><?= lang('custommodels_no_models') ?></h4>
    </div>
    <confirm-modal id="deleteModal" :title="'<?= lang('custommodels_confirm_delete_title') ?>'" v-on:notify="confirmCallback">
        <p>
            <?= lang('custommodels_confirm_delete_message') ?>
        </p>
    </confirm-modal>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
        data-delay="50" data-tooltip="<?= lang('custommodels_new_model_tooltip') ?>" href="{{base_url('admin/custommodels/nuevo/')}}">
        <i class="material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/CustomFormLists.js')}}"></script>
@endsection
