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
        'searchInputId' => 'usergroups-search',
        'refreshMethod' => 'getUserGroups()',
        'showViewToggle' => false,
        'navbarShow' => '!loader && usergroups.length > 0',
        'itemsExpr' => '',
        'section' => 'nav',
    ])
    <div class="configurations" v-cloak v-if="!loader && usergroups.length > 0 && filterUsergroups.length > 0">
        <div class="row">
            <div class="col s12">
                <table class="striped">
                    <thead>
                        <tr>
                            <th @click="sortData('name', usergroups);" v-bind:class="getSortData('name')">{{ lang('name') }}</th>
                            <th>{{ lang('description') }}</th>
                            <th>{{ lang('status') }}</th>
                            <th>{{ lang('options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(usergroup, index) in filterUsergroups" :key="usergroup.usergroup_id">
                            <td>@{{ usergroup.name }}</td>
                            <td>@{{ usergroup.description }}</td>
                            <td>
                                <span v-if="usergroup.status == 1" class="custom-badge status-published">{{ lang('usergroups_active') }}</span>
                                <span v-else class="custom-badge status-draft">{{ lang('usergroups_inactive') }}</span>
                            </td>
                            <td>
                                <a class="dropdown-trigger" href="#!" :data-target="'dropdown' + usergroup.usergroup_id" :aria-label="lang('options')">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul :id="'dropdown' + usergroup.usergroup_id" class="dropdown-content">
                                    @if(has_permisions('UPDATE_USERGROUP'))
                                    <li><a :href="base_url('admin/users/editGroup/' + usergroup.usergroup_id)">{{ lang('edit') }}</a></li>
                                    @endif
                                    @if(has_permisions('DELETE_USERGROUP'))
                                    <li v-if="Number(usergroup.usergroup_id) !== 1">
                                        <a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(usergroup, index);">{{ lang('delete') }}</a>
                                    </li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container" v-cloak v-if="!loader && usergroups.length === 0">
        <p class="page-header">{{ lang('usergroups_empty') }}</p>
        <p>{{ lang('usergroups_empty_hint') }}</p>
        @if(has_permisions('CREATE_USERGROUP'))
        <a class="btn waves-effect" href="{{ base_url('admin/users/newGroup') }}">{{ lang('usergroups_new') }}</a>
        @endif
    </div>
    <div class="container" v-cloak v-if="!loader && usergroups.length > 0 && filter && filterUsergroups.length === 0">
        <p class="page-header">{{ lang('usergroups_no_results') }}</p>
        <a href="#!" class="btn-flat" v-on:click.prevent="resetFilter()">{{ lang('search_empty_cta') }}</a>
    </div>
    <confirm-modal
        id="deleteModal"
        title="<?= htmlspecialchars(lang('usergroups_confirm_delete'), ENT_QUOTES, 'UTF-8') ?>"
        v-on:notify="confirmCallback"
    >
        <p><?= htmlspecialchars(lang('usergroups_confirm_delete'), ENT_QUOTES, 'UTF-8') ?></p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_USERGROUP'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a
        class="btn-floating btn-large st-accent waves-effect tooltipped"
        data-position="left"
        data-delay="50"
        data-tooltip="<?= htmlspecialchars(lang('usergroups_new'), ENT_QUOTES, 'UTF-8') ?>"
        aria-label="<?= htmlspecialchars(lang('usergroups_new'), ENT_QUOTES, 'UTF-8') ?>"
        href="{{ base_url('admin/users/newGroup') }}"
    >
        <i class="large material-icons">add</i>
    </a>
</div>
@endif
<script>
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
    name: <?= json_encode(lang('name')) ?>,
    description: <?= json_encode(lang('description')) ?>,
    status: <?= json_encode(lang('status')) ?>,
    options: <?= json_encode(lang('options')) ?>,
    edit: <?= json_encode(lang('edit')) ?>,
    delete: <?= json_encode(lang('delete')) ?>,
    search_empty_cta: <?= json_encode(lang('search_empty_cta')) ?>,
    usergroups_new: <?= json_encode(lang('usergroups_new')) ?>,
    usergroups_empty: <?= json_encode(lang('usergroups_empty')) ?>,
    usergroups_empty_hint: <?= json_encode(lang('usergroups_empty_hint')) ?>,
    usergroups_no_results: <?= json_encode(lang('usergroups_no_results')) ?>,
    usergroups_confirm_delete: <?= json_encode(lang('usergroups_confirm_delete')) ?>,
    usergroups_cannot_delete_has_users: <?= json_encode(lang('usergroups_cannot_delete_has_users')) ?>,
    usergroups_active: <?= json_encode(lang('usergroups_active')) ?>,
    usergroups_inactive: <?= json_encode(lang('usergroups_inactive')) ?>,
    usergroups_unexpected_error: <?= json_encode(lang('usergroups_unexpected_error')) ?>,
    toast_error: <?= json_encode(lang('usergroups_unexpected_error')) ?>
});
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/UserGroupsComponent.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
