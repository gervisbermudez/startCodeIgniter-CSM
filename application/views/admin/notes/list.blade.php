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
    <nav class="page-navbar" v-cloak v-show="!loader && notes.length > 0">
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
                        <li><a href="#!">Archivo</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="notes" v-cloak v-if="!loader && notes.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(note, index) in filterNotes" :key="index">
                            <td>@{{note.title}}</td>
                            <td>@{{note.type}}</td>
                            <td><a :href="base_url('admin/users/ver/' + note.user_id)">@{{note.user.get_fullname()}}</a></td>
                            <td>
                                @{{note.date_publish ? note.date_publish : note.date_create}}
                            </td>
                            <td>
                                <i v-if="note.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publicado">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Borrador">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + note.note_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + note.note_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/sitenotes/editar/' + note.note_id)">Editar</a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(note, index);">Borrar</a></li>
                                    <li v-if="note.status == 2"><a :href="base_url('admin/sitenotes/preview?note_id=' + note.note_id)" target="_blank">Preview</a></li>
                                    <li><a :href="base_url(note.path)" target="_blank">Archivar</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(note, index) in filterNotes" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(note)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + note.note_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + note.note_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/sitenotes/editar/' + note.note_id)">Editar</a></li>
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(note, index);">Borrar</a></li>
                            <li v-if="note.status == 2"><a :href="base_url('admin/sitenotes/preview?note_id=' + note.note_id)" target="_blank">Preview</a></li>
                            <li><a :href="base_url(note.path)" target="_blank">Archivar</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(note.title)" target="_blank">@{{note.title}}</a> <i v-if="note.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(note)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li>
                                        Type: @{{note.type}}
                                    </li>
                                    <li class="truncate">
                                        Author: <a :href="base_url('admin/users/ver/' + note.user_id)">@{{note.user.user_data.nombre}} @{{note.user.user_data.apellido}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{note.title}}
                        </span>
                        <span class="subtitle">
                            @{{note.subtitle}}
                        </span>
                        <ul>
                            <li><b>Fecha de publicacion:</b> <br> @{{note.date_publish ? note.date_publish : note.date_create}}</li>
                            <li><b>Estado:</b>
                                <span v-if="note.status == 1">
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
    <div class="container" v-if="!loader && notes.length == 0" v-cloak>
        <h4>No hay Notas</h4>
    </div>
    <confirm-modal
        id="deleteModal"
        title="Confirmar Borrar"
        v-on:notify="confirmCallback"
    >
        <p>
            ¿Desea borrar Nota?
        </p>
    </confirm-modal>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear nota" href="<?php echo base_url('admin/sitenotes/nueva/') ?>">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/NotesLists.js')}}"></script>
@endsection
