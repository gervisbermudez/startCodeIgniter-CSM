@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    <div class="col s12 center" v-show="loader">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'fragments-search',
        'refreshMethod' => 'getFragments()',
        'itemsExpr' => 'filterFragments',
    ])
    <div class="pages fragments" v-cloak v-if="!loader && fragments.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th><?php echo lang('name'); ?></th>
                            <th><?php echo lang('type'); ?></th>
                            <th><?php echo lang('author'); ?></th>
                            <th><?php echo lang('publish_date'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('options'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(fragment, index) in filterFragments" :key="index">
                            <td>@{{fragment.name}}</td>
                            <td>@{{fragment.type}}</td>
                            <td>
                                <a v-if="fragment.user" :href="base_url('admin/users/ver/' + fragment.user_id)">@{{fragment.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{fragment.date_create}}
                            </td>
                            <td>
                                <i v-if="fragment.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('published'); ?>">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('draft'); ?>">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + fragment.fragment_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + fragment.fragment_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/fragments/edit/' + fragment.fragment_id)" class="tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('edit'); ?>">{{ lang('edit') }}</a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(fragment, index);">{{ lang('delete') }}</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(fragment, index) in filterFragments" :key="index">
                <div class="card page-card">
                    <div class="card-content">
                        <div>
                            <span class="card-title">
                                <a :href="base_url('admin/fragments/edit/' + fragment.fragment_id)">@{{fragment.name}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'fragment'])
                                <a class="dropdown-trigger right" href='#!' :data-target='"dropdown-card" + fragment.fragment_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown-card" + fragment.fragment_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/fragments/edit/' + fragment.fragment_id)" class="tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('edit'); ?>"><?php echo lang('edit'); ?></a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(fragment, index);"><?php echo lang('delete'); ?></a></li>
                                </ul>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(fragment)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info v-if="fragment.user" :user="fragment.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{fragment.name}}
                        </span>
                        <ul>
                            <li><b><?php echo lang('publish_date'); ?>:</b> <br> @{{fragment.date_create}}</li>
                            <li><b><?php echo lang('status'); ?>:</b>
                                <span v-if="fragment.status == 1">
                                    <?php echo lang('published'); ?>
                                </span>
                                <span v-else>
                                    <?php echo lang('draft'); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container center" v-if="!loader && fragments.length == 0 && !filter" v-cloak>
        <i class="material-icons large grey-text">short_text</i>
        <p class="page-header">{{ lang('fragments_empty') }}</p>
        <a href="{{ base_url('admin/fragments/new/') }}" class="btn">{{ lang('fragments_empty_cta') }}</a>
    </div>
    @include('admin.components.pagination')
    <confirm-modal
        id="deleteModal"
        title="<?php echo lang('confirm_delete'); ?>"
        v-on:notify="confirmCallback"
    >
        <p>
            <?php echo lang('delete_fragment_question'); ?>
        </p>
    </confirm-modal>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('create_fragment'); ?>" href="<?php echo base_url('admin/fragments/new/') ?>">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/FragmentsLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
