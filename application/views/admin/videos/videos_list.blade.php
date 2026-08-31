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
            'searchInputId' => 'videos-search',
            'refreshMethod' => 'getVideos(currentStatus)',
            'navbarShow' => '!loader && videos.length > 0',
            'itemsExpr' => 'filterAll',
        ])
        <div class="row">
            <div class="col s12">
                <div class="status-filters" v-cloak v-show="!loader">
                    <div class="status-chip" :class="{active: currentStatus === null}" @click="getVideos(null)">
                        <?= lang('menu_all') ?>
                    </div>
                    <div class="status-chip" :class="{active: currentStatus === 1}" @click="getVideos(1)">
                        <?= lang('published') ?>
                    </div>
                    <div class="status-chip" :class="{active: currentStatus === 2}" @click="getVideos(2)">
                        <?= lang('draft') ?>
                    </div>
                    <div class="status-chip" :class="{active: currentStatus === 3}" @click="getVideos(3)">
                        <?= lang('archived') ?>
                    </div>
                    <div class="status-chip" :class="{active: currentStatus === 0}" @click="getVideos(0)">
                        <?= lang('deleted') ?>
                    </div>
                </div>
            </div>
        </div>
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
                                    <i v-else-if="video.status == 2" class="material-icons tooltipped" data-position="left"
                                        data-delay="50" :data-tooltip="lang('videos_draft')">edit</i>
                                    <i v-else-if="video.status == 3" class="material-icons tooltipped" data-position="left"
                                        data-delay="50" data-tooltip="<?= lang('archived') ?>">archive</i>
                                    <i v-else class="material-icons tooltipped" data-position="left"
                                        data-delay="50" data-tooltip="<?= lang('deleted') ?>">delete_outline</i>
                                </td>
                                <td>
                                    <a class='dropdown-trigger' href='#!'
                                        :data-target='"dropdown" + (video.id || video.video_id)'><i
                                            class="material-icons">more_vert</i></a>
                                    <ul :id='"dropdown" + (video.id || video.video_id)' class='dropdown-content'>
                                        @if(has_permisions('UPDATE_VIDEO'))
                                        <li><a
                                                :href="base_url('admin/videos/editar/' + (video.id || video.video_id))"><?= lang('edit') ?></a>
                                        </li>
                                        @endif
                                        @if(has_permisions('DELETE_VIDEO'))
                                        <li><a class="modal-trigger" href="#deleteModal"
                                                v-on:click="tempDelete(video, index);"><?= lang('delete') ?></a></li>
                                        @endif
                                        <li><a :href="base_url('/admin/videos/ver/' + (video.id || video.video_id))"><?= lang('videos_view') ?></a></li>
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
                                @if(has_permisions('UPDATE_VIDEO'))
                                <li><a
                                        :href="base_url('admin/videos/editar/' + (video.id || video.video_id))">{{ lang('edit') }}</a>
                                </li>
                                @endif
                                @if(has_permisions('DELETE_VIDEO'))
                                <li><a class="modal-trigger" href="#deleteModal"
                                        v-on:click="tempDelete(video, index);">{{ lang('delete') }}</a></li>
                                @endif
                                <li><a :href="base_url('/admin/videos/ver/' + (video.id || video.video_id))">{{ lang('videos_view') }}</a></li>
                            </ul>
                        </div>
                        <div class="card-content">
                            <div>
                                <span class="card-title"><a
                                        :href="base_url('/admin/videos/ver/' + (video.id || video.video_id))">@{{ video.nam || video.nombre || video.video_id }}</a>
                                    @include('admin.components.entity_card_badges', ['item' => 'video'])
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
                                    <span v-else-if="video.status == 2"><?= lang('videos_draft') ?></span>
                                    <span v-else-if="video.status == 3"><?= lang('archived') ?></span>
                                    <span v-else><?= lang('deleted') ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container center" v-cloak v-show="!loader && videos.length == 0">
            <i class="material-icons large grey-text">video_library</i>
            <p class="page-header"><?= lang('videos_empty') ?></p>
            <a href="<?= base_url('admin/videos/nuevo') ?>" class="btn"><?= lang('videos_empty_cta') ?></a>
        </div>
        <confirm-modal id="deleteModal" title="<?= lang('delete_video_title') ?>" v-on:notify="confirmCallback">
            <p><?= lang('delete_video_confirm') ?></p>
        </confirm-modal>
    </div>
@if(has_permisions('CREATE_VIDEO'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left"
            data-delay="50" data-tooltip="<?= lang('videos_create') ?>" href="<?= base_url('admin/videos/nuevo/') ?>">
            <i class="large material-icons">add</i>
        </a>
</div>
@endif
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/VideosLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
