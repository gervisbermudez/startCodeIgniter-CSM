@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_users',
        'ledeKey' => 'users_lede',
    ])
    <div class="col s12 center" v-show="loader">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'users-search',
        'refreshMethod' => 'getUsers()',
        'navbarShow' => '!loader',
        'itemsExpr' => 'filterUsers',
    ])
    <div class="container" v-if="tableView && filterUsers.length > 0" v-cloak v-show="!loader">
        <div class="row">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th @click="sortData('username', users);" v-bind:class="getSortData('username')"><?= lang('username') ?></th>
                            <th><?= lang('name') ?></th>
                            <th @click="sortData('role', users);" v-bind:class="getSortData('role')"><?= lang('users_role') ?></th>
                            <th @click="sortData('lastseen', users);" v-bind:class="getSortData('lastseen')"><?= lang('users_last_seen') ?></th>
                            <th @click="sortData('date_create', users);" v-bind:class="getSortData('date_create')"><?= lang('creation_date') ?></th>
                            <th @click="sortData('status', users);" v-bind:class="getSortData('status')"><?= lang('status') ?></th>
                            <th><?= lang('options') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in filterUsers" :key="user.user_id">
                            <td><a :href="base_url('admin/users/ver/' + user.user_id)">@{{ user.username }}</a></td>
                            <td>@{{ userDisplayName(user) }}</td>
                            <td>@{{ user.role }}</td>
                            <td>@{{ user.lastseen }}</td>
                            <td>@{{ user.date_create }}</td>
                            <td>
                                <i v-if="user.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?= htmlspecialchars(lang('public'), ENT_QUOTES, 'UTF-8') ?>">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?= htmlspecialchars(lang('private'), ENT_QUOTES, 'UTF-8') ?>">lock</i>
                            </td>
                            <td>
                                <a class="dropdown-trigger" href="#!" :data-target="'dropdown-table' + user.user_id" aria-label="<?= htmlspecialchars(lang('options'), ENT_QUOTES, 'UTF-8') ?>"><i class="material-icons">more_vert</i></a>
                                <ul :id="'dropdown-table' + user.user_id" class="dropdown-content">
                                    @if(has_permisions('UPDATE_USER'))
                                    <li><a :href="base_url('admin/users/edit/' + user.user_id)"><?= lang('btn_edit') ?></a></li>
                                    @endif
                                    @if(has_permisions('DELETE_USER'))
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(user, index);"><?= lang('btn_delete') ?></a></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="users-grid" v-else-if="!tableView && filterUsers.length > 0" v-cloak v-show="!loader">
        <user-card
            v-for="(user, index) in filterUsers"
            :key="user.user_id"
            :user="user"
            :index="index"
            v-on:tempDelete="tempDelete"
        />
    </div>
    <div class="container center" v-if="!loader && users.length == 0 && !filter" v-cloak>
        <i class="material-icons large grey-text" aria-hidden="true">people</i>
        <p class="page-header"><?= lang('users_empty') ?></p>
        @if(has_permisions('CREATE_USER'))
        <a class="btn waves-effect st-accent" href="{{ base_url('admin/users/add/') }}"><?= lang('users_empty_cta') ?></a>
        @endif
    </div>
    <confirm-modal
        id="deleteModal"
        title="<?= htmlspecialchars(lang('confirm_delete'), ENT_QUOTES, 'UTF-8') ?>"
        v-on:notify="confirmCallback"
    >
        <p>
            <?= htmlspecialchars(lang('users_confirm_delete'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_USER'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left" data-delay="50" data-tooltip="<?= htmlspecialchars(lang('tooltip_new_user'), ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars(lang('tooltip_new_user'), ENT_QUOTES, 'UTF-8') ?>" href="{{ base_url('admin/users/add/') }}">
        <i class="large material-icons">add</i>
    </a>
</div>
@endif
<script type="text/x-template" id="user-card-template">
    <div class="card page-card user-card">
        <div class="card-image">
            <div class="card-image-container">
                <img :src="user.get_avatarurl()" :alt="displayName">
            </div>
            <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href="#!" :data-target="'dropdown' + user.user_id" aria-label="<?= htmlspecialchars(lang('options'), ENT_QUOTES, 'UTF-8') ?>">
                <i class="material-icons">more_vert</i>
            </a>
            <ul :id="'dropdown' + user.user_id" class="dropdown-content">
                @if(has_permisions('UPDATE_USER'))
                <li><a :href="base_url('admin/users/edit/' + user.user_id)"><?= lang('btn_edit') ?></a></li>
                @endif
                @if(has_permisions('DELETE_USER'))
                <li><a class="modal-trigger" href="#deleteModal" v-on:click="requestDelete"><?= lang('btn_delete') ?></a></li>
                @endif
            </ul>
        </div>
        <div class="card-content">
            <span class="card-title">
                <a :href="base_url('admin/users/ver/' + user.user_id)">@{{ displayName }}</a>
                <div class="entity-card-badges">
                    <span v-if="user.status == 1" class="custom-badge visibility-public">
                        <i class="material-icons tiny">public</i> <?= lang('public') ?>
                    </span>
                    <span v-else class="custom-badge visibility-private">
                        <i class="material-icons tiny">lock</i> <?= lang('private') ?>
                    </span>
                </div>
            </span>
            <div class="card-info">
                <div class="card-info-row" v-if="hasValue(user.role)"><i class="material-icons">account_box</i> @{{ user.role }}</div>
                <div class="card-info-row" v-if="hasValue(user.lastseen)"><i class="material-icons">access_time</i> @{{ user.lastseen }}</div>
                <div class="card-info-row" v-if="phone"><i class="material-icons">local_phone</i> @{{ phone }}</div>
            </div>
        </div>
    </div>
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/UserComponent.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
