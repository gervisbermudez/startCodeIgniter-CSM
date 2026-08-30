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
        'itemsExpr' => 'filterUsergroups',
    ])
    <div class="configurations" v-cloak v-if="!loader && usergroups.length > 0">
        <div class="row">
            <div class="col s12">
                <table class="striped">
                    <thead>
                        <tr>
                            <th @click="sortData('name', usergroups);" v-bind:class="getSortData('name')">Name</th>
                            <th @click="sortData('level', usergroups);" v-bind:class="getSortData('level')" >Level</th>
                            <th>Description</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(usergroup, index) in filterUsergroups" :key="index">
                            <td>@{{usergroup.name}}</td>
                            <td>
                                <div>
                                    @{{usergroup.level}}
                                </div>
                            </td>
                            <td>@{{usergroup.description}}</td>
                            <td><a :href="base_url('admin/users/ver/' + usergroup.user_id)">
                            @{{usergroup.user.get_fullname()}}</a>
                            </td>
                            <td>
                                @{{usergroup.date_publish ? usergroup.date_publish : usergroup.date_create}}
                            </td>
                            <td>
                                <i v-if="usergroup.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Permitido">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="No permitido">not_interested</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + usergroup.usergroup_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + usergroup.usergroup_id' class='dropdown-content'>
                                    <li><a :href="'/admin/users/editGroup/' + usergroup.usergroup_id"><?php echo lang('edit'); ?></a></li>
                                    <li><a href="#!" v-on:click="deletePage(usergroup, index);">Borrar</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && usergroups.length == 0" v-cloak>
        <h4>No hay datos para mostrar</h4>
    </div>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/UserGroupsComponent.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
