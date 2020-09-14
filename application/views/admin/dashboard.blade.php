@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/dashboard.min.css')?>">
@endsection

@section('content')
<div class="container large dashboard" id="root" v-cloak>
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
            <div class="col s6 m4">
                <div class="skeleton-card heightForSkeleton-card panel"></div>
            </div>
            <div class="col s6 m4">
                <div class="skeleton-card heightForSkeleton-card panel"></div>
            </div>
            <div class="col s6 m4">
                <div class="skeleton-card heightForSkeleton-card panel"></div>
            </div>
        </div>
    </div>
    <div class="row" v-show="!loader">
        <div class="col m8 s12">
            <div class="row">
                <div class="col s12">
                    <create-contents></create-contents>
                </div>
            </div>
        </div>
        <div class="col m4 s12">
            <users-collection></users-collection>
        </div>
    </div>
    <page-card v-show="!loader"></page-card>
    <div class="row" v-show="!loader">
        <div class="col m6 s12">
            <file-explorer-collection></file-explorer-collection>
        </div>
    </div>
</div>
<div class="fixed-action-btn">
    <a data-position="left" data-delay="50" data-tooltip="Formulario nuevo" class="btn-floating btn-large tooltipped red" href="{{base_url('admin/formularios/nuevo')}}">
        <i class="large material-icons">add</i>
    </a>
    <ul>
        <li><a data-position="left" data-delay="50" data-tooltip="Usuario nuevo" class="btn-floating tooltipped red" href="{{base_url('admin/usuarios/agregar')}}"><i class="material-icons">perm_identity</i></a></li>
        <li><a data-position="left" data-delay="50" data-tooltip="Pagina nueva" class="btn-floating tooltipped yellow darken-1" href="{{base_url('admin/paginas/nueva/')}}"><i class="material-icons">web</i></a></li>
        <li><a data-position="left" data-delay="50" data-tooltip="Album nuevo" class="btn-floating tooltipped green" href="{{base_url('admin/galeria/crearalbum/')}}"><i class="material-icons">publish</i></a></li>
        <li><a data-position="left" data-delay="50" data-tooltip="Evento nuevo" class="btn-floating tooltipped blue" href="{{ base_url('admin/eventos/agregar/') }}"><i class="material-icons">assistant</i></a></li>
    </ul>
</div>
@include('admin.components.pageCardComponent')
@include('admin.components.userCollectionComponent')
@include('admin.components.createContentsComponent')
@include('admin.components.fileExplorerCollectionComponent')

@endsection

@section('footer_includes')
<script src="<?=base_url('public/js/components/dashboardBundle.min.js?v=' . ADMIN_VERSION)?>"></script>
@endsection
