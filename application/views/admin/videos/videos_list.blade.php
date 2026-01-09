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
        <nav class="page-navbar" v-cloak v-show="!loader && videos.length > 0">
            <div class="nav-wrapper">
                <form>
                    <div class="input-field">
                        <input class="input-search" type="search" placeholder="<?php echo lang('search'); ?>..." v-model="filter">
                        <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                        <i class="material-icons" v-on:click="resetFilter();">close</i>
                    </div>
                </form>
                <ul class="right hide-on-med-and-down">
                    <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                    <li><a href="#!" v-on:click="getVideos();"><i class="material-icons">refresh</i></a></li>
                    <li>
                        <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i
                                class="material-icons">more_vert</i></a>
                        <ul id='dropdown-options' class='dropdown-content'>
                            <li><a href="#!"><?php echo lang('archive'); ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="pages" v-cloak v-if="!loader && videos.length > 0">
            <div class="row" v-if="tableView">
                <div class="col s12">
                    <table>
                        <thead>
                            <tr>
                                <th><?= lang('videos_title') ?></th>
                                <th><?= lang('videos_description') ?></th>
                                <th><?= lang('videos_author') ?></th>
                                <th><?= lang('videos_date') ?></th>
                                <th><?= lang('videos_status') ?></th>
                                <th><?= lang('videos_options') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(video, index) in filterAll" :key="index">
                                <td>@{{ video.nam || video.nombre || video.video_id }}</td>
                                <td>@{{ getcontentText(video.description) }}</td>
                                <td>
                                    <a v-if="video.user" :href="video.user.get_profileurl()">@{{ video.user.get_fullname()
                                        }}</a>
                                    <span v-else>-</span>
                                </td>
                                <td>@{{ video.fecha ? video.fecha : (video.date_publish ? video.date_publish :
                                    video.date_create) }}</td>
                                <td>
                                    <i v-if="video.status == 1" class="material-icons tooltipped" data-position="left"
                                        data-delay="50" :data-tooltip="lang('videos_published')">publish</i>
                                    <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                        :data-tooltip="lang('videos_draft')">edit</i>
                                </td>
                                <td>
                                    <a class='dropdown-trigger' href='#!'
                                        :data-target='"dropdown" + (video.id || video.video_id)'><i
                                            class="material-icons">more_vert</i></a>
                                    <ul :id='"dropdown" + (video.id || video.video_id)' class='dropdown-content'>
                                        <li><a
                                                :href="base_url('admin/videos/editar/' + (video.id || video.video_id))"><?= lang('edit') ?></a>
                                        </li>
                                        <li><a class="modal-trigger" href="#deleteModal"
                                                v-on:click="tempDelete(video, index);"><?= lang('delete') ?></a></li>
                                        <li><a :href="base_url('/admin/videos/ver/' + (video.id || video.video_id))"
                                                target="_blank"><?= lang('videos_view') ?></a></li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" v-else>
                <div class="col s12 m4" v-for="(video, index) in filterAll" :key="index">
                    <div class="card page-card video">
                        <div class="card-image">
                            <div class="card-image-container">
                                <img :src="getVideoImagePath(video)" class="bottom" />
                            </div>
                            <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                                :data-target='"dropdown" + (video.id || video.video_id)'>
                                <i class="material-icons">more_vert</i></a>
                            <ul :id='"dropdown" + (video.id || video.video_id)' class='dropdown-content'>
                                <li><a
                                        :href="base_url('admin/videos/editar/' + (video.id || video.video_id))">{{ lang('edit') }}</a>
                                </li>
                                <li><a class="modal-trigger" href="#deleteModal"
                                        v-on:click="tempDelete(video, index);">{{ lang('delete') }}</a></li>
                                <li><a :href="base_url('/admin/videos/ver/' + (video.id || video.video_id))"
                                        target="_blank">{{ lang('archive') }}</a></li>
                            </ul>
                        </div>
                        <div class="card-content">
                            <div>
                                <span class="card-title"><a
                                        :href="base_url('/admin/videos/ver/' + (video.id || video.video_id))">@{{ video.nam || video.nombre || video.video_id }}</a>
                                    <i v-if="video.status == 1" class="material-icons tooltipped" data-position="left"
                                        data-delay="50" data-tooltip="<?= lang('videos_public') ?>">public</i>
                                    <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                        data-tooltip="<?= lang('videos_private') ?>">lock</i>
                                </span>
                                <div class="card-info">
                                    <p>@{{ getcontentText(video.description || video.descripcion) }}</p>
                                    <span class="activator right"><i class="material-icons">more_vert</i></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-reveal">
                                <span class="card-title grey-text text-darken-4"><i
                                    class="material-icons right">close</i>@{{ video.nam || video.nombre || video.video_id }}</span>
                                <span class="subtitle">@{{ getcontentText(video.description || video.descripcion) }}</span>
                            <ul>
                                <li><b><?= lang('videos_publish_date') ?>:</b> <br> @{{video.date_publish ?
                                    video.date_publish :
                                    video.date_create}}</li>
                                <li><b><?= lang('videos_status') ?>:</b>
                                    <span v-if="video.status == 1"><?= lang('videos_published') ?></span>
                                    <span v-else><?= lang('videos_draft') ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-cloak v-show="!loader && videos.length == 0" class="container">
            <p>
                <?= lang('videos_none_yet') ?>
            </p>
        </div>
        <confirm-modal id="deleteModal" title="<?= lang('delete_video_title') ?>" v-on:notify="confirmCallback">
            <p><?= lang('delete_video_confirm') ?></p>
        </confirm-modal>
    </div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
            data-delay="50" data-tooltip="<?= lang('videos_create') ?>" href="<?= base_url('admin/videos/nuevo/') ?>">
            <i class="large material-icons">add</i>
        </a>
</div>
@endsection