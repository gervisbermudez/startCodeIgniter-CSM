@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
<div id="root">
    <nav class="page-navbar" v-cloak v-if="!loader">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                    <input id="search" type="search" v-model="filter">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetSearch();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                <li><a href="#!" v-on:click="getUsers();"><i class="material-icons">refresh</i></a></li>
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
    <div class="container">
        <div class="row">
            <div class="col s12">
                <div class="col s12 center" v-bind:class="{ hide: !loader }">
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
                <div class="row" v-if="tableView && users.length > 0" v-cloak>
                    <div class="col s12">
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>lastseen</th>
                                    <th>Date Create</th>
                                    <th>Status</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(user, index) in filterUsers" :key="index">
                                    <td><a :href="base_url('admin/usuarios/ver/' + user.user_id)">@{{user.username}}</a></td>
                                    <td>@{{user.user_data.nombre + ' ' + user.user_data.apellido}}</td>
                                    <td>@{{user.role}}</td>
                                    <td>@{{user.lastseen}}</td>
                                    <td>@{{user.date_create}}</td>
                                    <td>
                                        <i v-if="user.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                                        <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
                                    </td>
                                    <td>
                                        <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + user.user_id'><i class="material-icons">more_vert</i></a>
                                        <ul :id='"dropdown" + user.user_id' class='dropdown-content'>
                                            <li><a :href="base_url('admin/usuarios/edit/' + user.user_id)">Editar</a></li>
                                            <li><a href="#!" v-on:click="deletePage(page, index);">Borrar</a></li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-else v-cloak>
                    <div class="col s12 m4" v-cloak v-show="!loader" v-for="user in filterUsers">
                        <user-card :user="user" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear Usuario" href="{{base_url('admin/usuarios/agregar/')}} ">
        <i class="large material-icons">add</i>
    </a>
</div>
<script type="text/x-template" id="user-card-template">
    <div class="card user-card">
		<div class="card-image">
			<div class="card-image-container">
				<img v-if="user.user_data.avatar"
					:src="getAvatarUrl()">
				<img v-else src="/public/img/default.jpg" />
			</div>
			<span class="card-title"><a :href="base_url('admin/usuarios/ver/' + user.user_id)" class="white-text">@{{user.user_data.nombre + ' ' + user.user_data.apellido}}</a></span>
				<a class='btn-floating halfway-fab waves-effect waves-light dropdown-trigger' href='#!' :data-target='"dropdown" + user.user_id'>
					<i class="material-icons">more_vert</i></a>
				<ul :id='"dropdown" + user.user_id' class='dropdown-content'>
					<li><a :href="base_url('admin/usuarios/edit/' + user.user_id)">Editar</a></li>
					<li><a href="#!" v-on:click="deletePage(page, index);">Borrar</a></li>
				</ul>
			</a>
		</div>
		<div class="card-content">
			<div>
				<div class="card-info"><i class="material-icons">account_box</i> @{{user.role}}<br></div>
				<div class="card-info"><i class="material-icons">access_time</i> @{{user.lastseen}}<br>
				</div>
				<div class="card-info"><i class="material-icons">local_phone</i> @{{user.user_data.telefono}}</div>
			</div>
		</div>
	</div>
</script>
@endsection
