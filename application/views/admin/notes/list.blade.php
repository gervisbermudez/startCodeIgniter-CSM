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
        'searchInputId' => 'notes-search',
        'refreshMethod' => 'getNotes()',
        'itemsExpr' => 'filterNotes',
    ])
    <div class="pages notes" v-cloak v-if="!loader && notes.length > 0">
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
                            <td>
                                <a v-if="note.user" :href="base_url('admin/users/ver/' + note.user_id)">@{{note.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{note.date_publish ? note.date_publish : note.date_create}}
                            </td>
                            <td>
                                <i v-if="note.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('published'); ?>">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('draft'); ?>">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + note.note_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + note.note_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/sitenotes/editar/' + note.note_id)"><?php echo lang('edit'); ?></a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(note, index);">Borrar</a></li>
                                    <li v-if="note.status == 2"><a :href="base_url('admin/sitenotes/preview?note_id=' + note.note_id)" target="_blank">Preview</a></li>
                                    <li v-if="note.path"><a :href="base_url(note.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
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
                            <li v-if="note.path"><a :href="base_url(note.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url('admin/sitenotes/editar/' + note.note_id)">@{{note.title}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'note'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(note)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info v-if="note.user" :user="note.user" />
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
    <div class="container" v-if="!loader && notes.length == 0 && !filter" v-cloak>
        <h4>No hay Notas</h4>
    </div>
    @include('admin.components.pagination')
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
<script src="{{base_url('resources/components/NotesLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
