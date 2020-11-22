@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>
    <nav class="page-navbar" v-cloak v-show="!loader && usergroups.length > 0">
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

                <li><a href="#!" v-on:click="getUserGroups();"><i class="material-icons">refresh</i></a></li>
                <li>
                    <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i class="material-icons">more_vert</i></a>
                    <ul id='dropdown-options' class='dropdown-content'>
                        <li><a href="#!">Archivo</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
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
                            <td><a :href="base_url('admin/usuarios/ver/' + usergroup.user_id)">
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
                                    <li><a :href="'/admin/usuarios/editGroup/' + usergroup.usergroup_id">Editar</a></li>
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
@include('admin.components.configurationcomponent')
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/usergroupscomponent.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
