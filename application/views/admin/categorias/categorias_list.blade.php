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
    <nav class="page-navbar" v-cloak v-show="!loader && categories.length > 0">
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
                <li><a href="#!" v-on:click="getPages();"><i class="material-icons">refresh</i></a></li>
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
    <div class="categories" v-cloak v-if="!loader && categories.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(categorie, index) in filterCategories" :key="index">
                            <td>@{{categorie.name}}</td>
                            <td>@{{categorie.type}}</td>
                            <td><a :href="base_url('admin/usuarios/ver/' + categorie.user_id)">@{{categorie.user.get_fullname()}}</a></td>
                            <td>
                                @{{categorie.date_publish ? categorie.date_publish : categorie.date_create}}
                            </td>
                            <td>
                                <i v-if="categorie.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publicado">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Borrador">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + categorie.categorie_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + categorie.categorie_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/categorias/editar/' + categorie.categorie_id)">Editar</a></li>
                                    <li><a href="#!" v-on:click="deletePage(page, index);">Borrar</a></li>
                                    <li v-if="categorie.status == 2"><a :href="base_url('admin/categorias/preview?categorie_id=' + categorie.categorie_id)" target="_blank">Preview</a></li>
                                    <li><a :href="base_url(categorie.path)" target="_blank">Archivar</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(categorie, index) in filterCategories" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(categorie)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + categorie.categorie_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + categorie.categorie_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/categorias/editar/' + categorie.categorie_id)">Editar</a></li>
                            <li><a href="#!" v-on:click="deletePage(page, index);">Borrar</a></li>
                            <li v-if="categorie.status == 2"><a :href="base_url('admin/categorias/preview?categorie_id=' + categorie.categorie_id)" target="_blank">Preview</a></li>
                            <li><a :href="base_url(categorie.path)" target="_blank">Archivar</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(categorie.name)" target="_blank">@{{categorie.name}}</a> <i v-if="categorie.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(categorie)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li>
                                        Type: @{{categorie.type}}
                                    </li>
                                    <li class="truncate">
                                        Author: <a :href="base_url('admin/usuarios/ver/' + categorie.user_id)">@{{categorie.user.user_data.nombre}} @{{categorie.user.user_data.apellido}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{categorie.name}}
                        </span>
                        <span class="subtitle">
                            @{{categorie.subtitle}}
                        </span>
                        <ul>
                            <li><b>Fecha de publicacion:</b> <br> @{{categorie.date_publish ? categorie.date_publish : categorie.date_create}}</li>
                            <li><b>Estado:</b>
                                <span v-if="categorie.status == 1">
                                    Publicado
                                </span>
                                <span v-else>
                                    Borrador
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && categories.length == 0" v-cloak>
        <h4>No hay Categorias</h4>
    </div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear categoria" href="<?php echo base_url('admin/categorias/nueva/') ?>">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/CategoriesLists.min.js')}}"></script>
@endsection
