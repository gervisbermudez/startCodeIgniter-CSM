@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/dashboard.min.css')?>">
@endsection

@section('content')
<div class="container large dashboard" :class="{showLoader: loader}" id="root" v-cloak>
    <div v-show="loader">
        <div class="row">
            <div class="col s8">
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-list heightForSkeleton-list">&nbsp;</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6 ">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                    <div class="col s6 ">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6 ">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                    <div class="col s6">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-left" v-show="!loader">
        <div class="overview">
            <span>{{ lang('dashboard_overview') }}</span>
        </div>
        <div class="welcome">
            <div class="welcome_container">
                <div class="welcome_message">
                    <span class="welcome_big">{{ lang('dashboard_welcome_back') }}</span> <br />
                    <span>{{userdata('nombre') }} {{userdata('apellido') }}</span>
                </div>
                <div class="columns">
                    <a href="{{ base_url('admin/users') }}" class="colum st-teal" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">people</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{users.length}}</b></div>
                            <div class="text-st-white">{{ lang('menu_users') }}</div>
                        </div>
                    </a>
                    <a href="{{ base_url('admin/pages') }}" class="colum st-pink" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">web</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{pages.length}}</b></div>
                            <div class="text-st-white">Pages</div>
                        </div>
                    </a>
                    <a href="{{ base_url('admin/files') }}" class="colum st-gray" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">markunread_mailbox</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{files.length}}</b></div>
                            <div class="text-st-white">Files</div>
                        </div>
                    </a>
                    <a href="{{ base_url('admin/events') }}" class="colum st-gray-light" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-gray">assistant</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-gray"><b>3</b></div>
                            <div class="text-st-gray">Events</div>
                        </div>
                    </a>
                </div>
                <div class="img">
                    <img src="{{url('public/img/admin/dashboard/undraw_charts.png')}}" alt="undraw_charts">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12">
                        <div class="panel">
                            <div class="title">
                                <h5>{{ lang('dashboard_statistics') }}</h5>
                            </div>
                            <div class="charts">
                                <div class="chart chart-1">
                                    <div class="chart-header">
                                        {{ lang('dashboard_visits_per_day') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart1"></canvas>
                                        </div>
                                        <div class="col2">
                                            <span class="chart-title">VISITORS</span>
                                            <div class="chart-big-number">20.345</div>
                                            <div class="chart-description">Views 53%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-2">
                                    <div class="chart-header">
                                        {{ lang('dashboard_requests_count') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col2">
                                            <span class="chart-title">VISITORS</span>
                                            <div class="chart-big-number">20.345</div>
                                            <div class="chart-description">Views 53%</div>
                                        </div>
                                        <div class="col1 ">
                                            <canvas id="myChart2"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-3">
                                    <div class="chart-header">
                                        {{ lang('dashboard_devices') }}
                                        <div class="chart-description">@{{graphs.devices.labelMayor}}
                                            @{{graphs.devices.porcentajeMayor}}%</div>
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart3"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-4">
                                    <div class="chart-header">
                                        {{ lang('dashboard_frequent_urls') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart4"></canvas>
                                        </div>
                                        <div class="col2">
                                            <span
                                                class="chart-title truncate">@{{graphs.urlFrecuentes.labelMayor}}</span>
                                            <div class="chart-big-number truncate">
                                                @{{graphs.urlFrecuentes.valorMasAlto}}</div>
                                            <div class="chart-description truncate">
                                                @{{graphs.urlFrecuentes.porcentajeMayor}}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col m6 l6 xl4 s12">
                <users-collection :users="users"></users-collection>
            </div>
            <div class="col m6 l6 xl4 s12">
                <file-explorer-collection :files="files"></file-explorer-collection>
            </div>
            <div class="col m6 l6 xl4 s12">
                <albumes-widget :albumes="albumes"></albumes-widget>
            </div>
            <div class="col s12">
                <create-contents :forms_types="forms_types" :content="content"></create-contents>
            </div>
        </div>
    </div>
    <div class="col-right st-white" v-show="!loader">
        <div class="row creator">
            <div class="col s12 ">
                <div class="creator-container">
                    <div class="user-avatar">
                        <img class="circle responsive-img" src="{{userdata('avatar')}}" />
                        <span class="truncate">{{ lang('dashboard_create_something') }} {{userdata('nombre')}}</span>
                    </div>
                    <div class="creator-input-field">
                        <textarea id="creator-input" placeholder="Type here..." class="materialize-textarea"
                            v-model="creator.content"></textarea>
                    </div>
                    <div class="creator-options">
                        <div class="options-icons">
                            <i class="material-icons tooltipped" v-for="mode in creator.modes" :key="mode"
                                :class="{'active': creator.mode == mode}" data-position="top" data-delay="500"
                                :data-tooltip="mode" @click="setCreatorMode(mode)">@{{creator.icons[mode]}}</i>
                        </div>
                        <button class="waves-effect waves-light btn" @click="saveDraft"
                            :class="{disabled: creator.content.length < 6}">Create<i
                                class="material-icons right">send</i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row drafts">
            <div class="col s12">
                <div class="title">
                    <span>{{ lang('dashboard_latest_drafts') }}</span>
                </div>
                <div class="collection">
                    <a v-for="(draf, index) in pages_draf" :key="index" :href="draf.link" class="collection-item"><span
                            class="badge">1</span><span class="truncate">@{{draf.title}}</span></a>
                </div>
            </div>
        </div>
        <div class="row timeline">
            <div class="col s12">
                <div class="title">
                    <span>{{ lang('dashboard_timeline') }}</span>
                </div>
                <div class="timeline-container">
                    <div class="card horizontal" v-for="(card, index) in timeline" :key="index">
                        <div v-if="card.imagen_file" class="card-image"
                            :style="'background-image: url(' + card.imagen_file.file_front_path + ');'"></div>
                        <div class="card-stacked">
                            <i class="material-icons card-options">more_vert</i>
                            <div class="card-header">
                                <img class="circle responsive-img"
                                    src="{{base_url()}}public/img/profile/default_profile_2.jpg" />
                                <div class="card-info">
                                    <span class="truncate title">@{{card.title}}</span>
                                    <span class="truncate datetime">@{{card.date}}</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p>@{{card.content}}</p>
                            </div>
                            <div class="card-action">
                                <a :href="card.link">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed-action-btn">
    <a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_form') }}'"
        class="btn-floating btn-large tooltipped red" href="{{base_url('admin/custommodels/nuevo')}}">
        <i class="large material-icons">add</i>
    </a>
    <ul>
        @if(has_permisions('CREATE_USER'))
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_user') }}'" class="btn-floating tooltipped red"
                href="{{base_url('admin/users/agregar')}}"><i class="material-icons">perm_identity</i></a></li>
        @endif
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_page') }}'"
                class="btn-floating tooltipped yellow darken-1" href="{{base_url('admin/pages/nueva/')}}"><i
                    class="material-icons">web</i></a></li>
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_album') }}'" class="btn-floating tooltipped green"
                href="{{base_url('admin/gallery/nuevo/')}}"><i class="material-icons">publish</i></a></li>
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_event') }}'" class="btn-floating tooltipped blue"
                href="{{ base_url('admin/events/agregar/') }}"><i class="material-icons">assistant</i></a></li>
    </ul>
</div>
@include('admin.components.pageCardComponent')
@include('admin.components.userCollectionComponent')
@include('admin.components.createContentsComponent')
@include('admin.components.fileExplorerCollectionComponent')
@include('admin.components.albumesWidgetComponent')

@endsection

@section('footer_includes')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?=base_url('resources/components/widget/albumesWidgetComponent.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/createContents.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/fileExplorerCollection.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/pageCardComponent.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/usersCollection.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/dashboardModule.js?v=' . ADMIN_VERSION)?>"></script>
@endsection