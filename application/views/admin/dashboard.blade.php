@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/dashboard.min.css')?>">
@endsection

@section('content')
<div class="container large dashboard" id="root" v-cloak>
    <div class="col-left" v-show="!loader">
        <div class="overview">
            <span>Overview</span>
        </div>
        <div class="welcome">
            <div class="welcome_container">
                <div class="welcome_message">
                    <span class="welcome_big">Welcome back,</span> <br />
                    <span>{{userdata('username') }}</span>
                </div>
                <div class="colum st-teal">
                    <div class="colum__icon">
                        <i class="material-icons text-st-white">people</i>
                    </div>
                    <div class="colum__description">
                        <div class="text-st-white"><b>3</b></div>
                        <div class="text-st-white">Usuarios</div>
                    </div>
                </div>
                <div class="colum st-pink">
                    <div class="colum__icon">
                        <i class="material-icons text-st-white">web</i>
                    </div>
                    <div class="colum__description">
                        <div class="text-st-white"><b>3</b></div>
                        <div class="text-st-white">Pages</div>
                    </div>
                </div>
                <div class="colum st-gray">
                    <div class="colum__icon">
                        <i class="material-icons text-st-white">markunread_mailbox</i>
                    </div>
                    <div class="colum__description">
                        <div class="text-st-white"><b>3</b></div>
                        <div class="text-st-white">Files</div>
                    </div>
                </div>
                <div class="colum st-gray-light">
                    <div class="colum__icon">
                        <i class="material-icons text-st-gray">assistant</i>
                    </div>
                    <div class="colum__description">
                        <div class="text-st-gray"><b>3</b></div>
                        <div class="text-st-gray">Events</div>
                    </div>
                </div>
                <div class="img">
                    <img src="{{url('public/img/admin/dashboard/undraw_charts.png')}}" alt="undraw_charts">
                </div>
            </div>
        </div>
        <div v-show="loader">
            <div class="row">
                <div class="col m8 s12">
                    <div class="row">
                        <div class="col s12">
                            <div class="skeleton-list heightForSkeleton-list panel"></div>
                        </div>
                    </div>
                </div>
                <div class="col m4 s12">
                    <div class="skeleton-blog heightForSkeleton-blog panel"></div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m4">
                    <div class="skeleton-card heightForSkeleton-card panel"></div>
                </div>
                <div class="col s12 m4">
                    <div class="skeleton-card heightForSkeleton-card panel"></div>
                </div>
                <div class="col s12 m4">
                    <div class="skeleton-card heightForSkeleton-card panel"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m8 s12">
                <div class="row">
                    <div class="col s12">
                        <create-contents :forms_types="forms_types" :content="content"></create-contents>
                    </div>
                </div>
            </div>
            <div class="col m4 s12">
                <users-collection :users="users"></users-collection>
            </div>
        </div>
        <div class="row">
            <div class="col m5 s12">
                <file-explorer-collection :files="files"></file-explorer-collection>
            </div>
            <div class="col m7 s12">
                <albumes-widget :albumes="albumes"></albumes-widget>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <page-card v-show="!loader" :pages="pages"></page-card>
            </div>
        </div>
    </div>
    <div class="col-right st-white" v-show="!loader">
    User
    </div>
</div>
<div class="fixed-action-btn">
    <a data-position="left" data-delay="50" data-tooltip="Formulario nuevo" class="btn-floating btn-large tooltipped red" href="{{base_url('admin/formularios/nuevo')}}">
        <i class="large material-icons">add</i>
    </a>
    <ul>
        @if(has_permisions('CREATE_USER'))
        <li><a data-position="left" data-delay="50" data-tooltip="Usuario nuevo" class="btn-floating tooltipped red" href="{{base_url('admin/usuarios/agregar')}}"><i class="material-icons">perm_identity</i></a></li>
        @endif
        <li><a data-position="left" data-delay="50" data-tooltip="Pagina nueva" class="btn-floating tooltipped yellow darken-1" href="{{base_url('admin/paginas/nueva/')}}"><i class="material-icons">web</i></a></li>
        <li><a data-position="left" data-delay="50" data-tooltip="Album nuevo" class="btn-floating tooltipped green" href="{{base_url('admin/galeria/nuevo/')}}"><i class="material-icons">publish</i></a></li>
        <li><a data-position="left" data-delay="50" data-tooltip="Evento nuevo" class="btn-floating tooltipped blue" href="{{ base_url('admin/eventos/agregar/') }}"><i class="material-icons">assistant</i></a></li>
    </ul>
</div>
@include('admin.components.pageCardComponent')
@include('admin.components.userCollectionComponent')
@include('admin.components.createContentsComponent')
@include('admin.components.fileExplorerCollectionComponent')
@include('admin.components.albumesWidgetComponent')

@endsection

@section('footer_includes')
<script src="<?=base_url('public/js/components/dashboardBundle.min.js?v=' . ADMIN_VERSION)?>"></script>
@endsection
