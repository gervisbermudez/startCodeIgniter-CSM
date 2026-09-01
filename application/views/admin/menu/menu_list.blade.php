@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_menus',
        'ledeKey' => 'menus_lede',
    ])
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'menus-search',
        'refreshMethod' => 'getMenus(currentStatus)',
        'navbarShow' => '!loader',
        'section' => 'nav-open',
    ])
    @include('admin.components.list_status_chips', [
        'click' => 'getMenus',
    ])
    @include('admin.components.page_navbar', [
        'refreshMethod' => 'getMenus(currentStatus)',
        'navbarShow' => '!loader',
        'itemsExpr' => 'filterMenus',
        'section' => 'nav-close',
    ])
    <div class="pages categories" v-cloak v-if="!loader && menus.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Template</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(menu, index) in filterMenus" :key="index">
                            <td>@{{menu.name}}</td>
                            <td>@{{menu.template}}</td>
                            <td>
                                <a v-if="menu.user" :href="base_url('admin/users/ver/' + menu.user_id)">@{{menu.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{menu.date_publish ? menu.date_publish : menu.date_create}}
                            </td>
                            <td>
                                <i v-if="menu.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publicado">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Borrador">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + menu.menu_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + menu.menu_id' class='dropdown-content'>
                                    @if(has_permisions('UPDATE_MENU'))
                                    <li><a :href="base_url('admin/menus/editar/' + menu.menu_id)"><?php echo lang('edit'); ?></a></li>
                                    @endif
                                    @if(has_permisions('DELETE_MENU'))
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(menu, index);">Borrar</a></li>
                                    @endif
                                    <li v-if="menu.status == 2"><a :href="base_url('admin/menus/preview?menu_id=' + menu.menu_id)" target="_blank">Preview</a></li>
                                    <li v-if="menu.path"><a :href="base_url(menu.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(menu, index) in filterMenus" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(menu)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + menu.menu_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + menu.menu_id' class='dropdown-content'>
                            @if(has_permisions('UPDATE_MENU'))
                            <li><a :href="base_url('admin/menus/editar/' + menu.menu_id)">Editar</a></li>
                            @endif
                            @if(has_permisions('DELETE_MENU'))
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(menu, index);">Borrar</a></li>
                            @endif
                            <li v-if="menu.path"><a :href="base_url(menu.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(menu.name)" target="_blank">@{{menu.name}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'menu'])
                            </span>
                            <div class="card-info">
                                <p></p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info v-if="menu.user" :user="menu.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{menu.name}}
                        </span>
                        <span class="subtitle">
                            @{{menu.template}}
                        </span>
                        <ul>
                            <li><b><?php echo lang('publish_date'); ?>:</b> <br> @{{menu.date_publish ? menu.date_publish : menu.date_create}}</li>
                            <li><b><?php echo lang('status'); ?>:</b>
                                <span v-if="menu.status == 1">
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
    <div class="container" v-if="!loader && menus.length == 0 && currentStatus === null && !filter" v-cloak>
        <h4>No hay Menus</h4>
    </div>
    @include('admin.components.list_filter_empty', [
        'showExpr' => '!loader && menus.length == 0 && (filter || currentStatus !== null)',
        'clearMethod' => 'resetFilter(); getMenus(null)',
    ])
    <confirm-modal 
        id="deleteModal" 
        title="Confirmar Borrar"
        v-on:notify="confirmCallback"
    >
        <p>
            ¿Desea borrar el menu?
        </p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_MENU'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Crear Menu" href="<?php echo base_url('admin/menus/nuevo/') ?>">
        <i class="large material-icons">add</i>
    </a>
</div>
@endif
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/MenuLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
