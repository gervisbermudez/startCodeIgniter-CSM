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
                    <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i class="material-icons">more_vert</i></a>
                    <!-- Dropdown Structure -->
                    <ul id='dropdown-options' class='dropdown-content'>
                        <li><a href="#!">Archivados</a></li>
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
                            <th>Form Name</th>
                            <th>Form Description</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Publish Date</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(form, index) in filterForms" :key="index">
                            <td>@{{form.form_name}}</td>
                            <td>@{{getcontentText(form.form_description)}}</td>
                            <td><a :href="base_url('admin/usuarios/ver/' + form.user_id)">@{{form.user.get_fullname()}}</a></td>
                            <td>
                                @{{form.date_publish ? form.date_publish : form.date_create}}
                            </td>
                                        <td>
                                            <i v-if="form.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Activo">publish</i>
                                            <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Inactivo">edit</i>
                                        </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown_" + form.form_custom_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown_" + form.form_custom_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/formularios/addData/' + form.form_custom_id)"> Agregar data</a></li>
                                    <li><a :href="base_url('admin/formularios/editForm/' + form.form_custom_id)"> Editar</a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(form, index);">Borrar</a></li>
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

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + form.form_custom_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + form.form_custom_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/formularios/addData/' + form.form_custom_id)"> Agregar data</a></li>
                            <li><a :href="base_url('admin/formularios/editForm/' + form.form_custom_id)"> Editar</a></li>
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(form, index);">Borrar</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(form.path)" target="_blank">@{{form.form_name}}</a> <i v-if="form.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                @{{getcontentText(form.form_description)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li class="truncate">
                                        Author: <a :href="base_url('admin/usuarios/ver/' + form.user_id)">@{{form.user.get_fullname()}}</a>
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
                            <li><b>Fecha de publicacion:</b> <br> @{{form.date_publish ? form.date_publish : form.date_create}}</li>
                            <li><b>Categorie:</b> @{{form.categorie}}</li>
                            <li><b>Subcategorie:</b> @{{form.subcategorie ? form.subcategorie : 'Ninguna'}}</li>
                            <li><b>Template:</b> @{{form.template}}</li>
                            <li><b>Type:</b> @{{form.page_type_name}}</li>
                            <li><b>Estado:</b>
                                <span v-if="form.status == 1">
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
    <div class="container" v-if="!loader && forms.length == 0" v-cloak>
        <h4>No hay formularios</h4>
    </div>
    <confirm-modal
        id="deleteModal"
        title="Confirmar Borrar"
        v-on:notify="confirmCallback"
    >
        <p>
            ¿Desea borrar el formulario?
        </p>
    </confirm-modal>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Nuevo Formulario" href="{{base_url('admin/formularios/nuevo/')}}">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/CustomFormLists.min.js')}}"></script>
@endsection
