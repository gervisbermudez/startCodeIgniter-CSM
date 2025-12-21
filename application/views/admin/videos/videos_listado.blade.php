@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
    <div class="row">
                <div id="root">
                    <div class="col s12 center" v-show="loader">
                        <br><br>
                        <preloader />
                    </div>

                    <nav class="page-navbar" v-cloak v-show="!loader && videos.length > 0">
                        <div class="nav-wrapper">
                            <form v-on:submit.prevent="">
                                <div class="input-field">
                                    <input class="input-search" type="search" placeholder="Buscar..." v-model="filter" style="width:100%">
                                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                                </div>
                            </form>
                            <ul class="right hide-on-med-and-down">
                                <li><a href="#!" v-on:click="getVideos(currentStatus);"><i class="material-icons">refresh</i></a></li>
                            </ul>
                        </div>
                    </nav>

                    <div class="row" v-cloak v-if="!loader && videos.length > 0">
                        <div class="col m6 s12 l4" v-for="(video, index) in filterAll" :key="video.id || video.video_id">
                            <div class="card" style="overflow: visible">
                                <a class='dropdown-trigger right' href='#!' :data-target="'dropdown' + (video.id || video.video_id)"><i class="material-icons">more_vert</i></a>
                                <ul :id="'dropdown' + (video.id || video.video_id)" class='dropdown-content'>
                                    <li v-if="hasPermission( 'UPDATE_VIDEO' )"><a :href="base_url('admin/videos/editar/' + (video.id || video.video_id))">Editar</a></li>
                                    <li v-if="hasPermission( 'DELETE_VIDEO' )"><a :href="'#' + modalid" class="modal-trigger" v-on:click="setTempVideo(video, index)">Eliminar</a></li>
                                    <li><a :href="base_url('admin/videos/ver/' + (video.id || video.video_id))">Ver</a></li>
                                </ul>
                                <div class="card-image waves-effect waves-block waves-light">
                                    <img class="activator" :src="getVideoImagePath(video)">
                                    <span class="card-title">@{{ video.nombre || video.nam || video.video_id }}</span>
                                </div>
                                <div class="card-content">
                                    <span class="activator grey-text text-darken-4">
                                        @{{ video.nombre || video.nam || video.video_id }}
                                        <i class="material-icons tooltipped right" data-position="left" data-delay="50" v-if="video.status == '1'">assignment_ind</i>
                                        <i class="material-icons tooltipped right" data-position="left" data-delay="50" v-else>lock</i>
                                    </span>
                                </div>
                                <div class="card-reveal">
                                    <span class="card-title grey-text text-darken-4"><i class="material-icons right">close</i></span>
                                    <p>Fecha: @{{ video.fecha || video.date_publish || video.date_create }}<br>
                                        @{{ video.youtubeid || video.youtube_id }}<br>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-cloak v-show="!loader && videos.length == 0">No hay videos todavía</div>

                    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
                        <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear video" href="<?php echo base_url('admin/videos/nuevo/') ?>">
                            <i class="large material-icons">add</i>
                        </a>
                    </div>

                    <!-- delete modal -->
                    <div id="deleteModal" class="modal">
                        <div class="modal-content">
                            <h4><i class="material-icons">warning</i> <?= lang('delete_video_title') ?></h4>
                            <p><?= lang('delete_video_confirm') ?></p>
                        </div>
                        <div class="modal-footer">
                            <a href="#!" data-action="acept" class="modal-action modal-close waves-effect waves-green btn-flat" v-on:click.prevent="confirmDelete(true)"><?= lang('accept') ?></a>
                            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat"><?= lang('cancel') ?></a>
                        </div>
                    </div>

                </div>
                @endsection