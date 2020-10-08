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
    <nav class="page-navbar" v-cloak v-show="!loader && albums.length > 0">
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
                        <li><a href="#!">one</a></li>
                        <li><a href="#!">two</a></li>
                        <li class="divider" tabindex="-1"></li>
                        <li><a href="#!">three</a></li>
                        <li><a href="#!"><i class="material-icons">view_module</i>four</a></li>
                        <li><a href="#!"><i class="material-icons">cloud</i>five</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="pages" v-cloak v-if="!loader && albums.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Album Title</th>
                            <th>Description</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(album, index) in filterData" :key="index">
                            <td>@{{album.name}}</td>
                            <td>@{{getcontentText(album.description)}}</td>
                            <td><a :href="album.user.get_profileurl()">@{{album.user.get_fullname()}}</a></td>
                            <td>
                                @{{album.date_publish ? album.date_publish : album.date_create}}
                            </td>
                            <td>
                                <i v-if="album.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publicado">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Borrador">edit</i>
                            </td>
                            <td>
                                <i v-if="album.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + album.page_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + album.page_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/paginas/editar/' + album.page_id)">Editar</a></li>
                                    <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                                    <li v-if="album.status == 2"><a :href="base_url('admin/paginas/preview?page_id=' + album.page_id)" target="_blank">Preview</a></li>
                                    <li><a :href="base_url('/admin/galeria/items/' + album.album_id)" target="_blank">Archivar</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(album, index) in filterData" :key="index">
                <div class="card page-card album">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(album, 0)" class="bottom"/>
                            <img :src="getPageImagePath(album, 1)" class="top"/>
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + album.page_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + album.page_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/paginas/editar/' + album.page_id)">Editar</a></li>
                            <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                            <li v-if="album.status == 2"><a :href="base_url('admin/paginas/preview?page_id=' + album.page_id)" target="_blank">Preview</a></li>
                            <li><a :href="base_url('/admin/galeria/items/' + album.album_id)" target="_blank">Archivar</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url('/admin/galeria/items/' + album.album_id)">@{{album.name}}</a> <i v-if="album.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(album.description)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info :user="album.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{album.name}}
                        </span>
                        <span class="subtitle">
                            @{{album.subtitle}}
                        </span>
                        <ul>
                            <li><b>Fecha de publicacion:</b> <br> @{{album.date_publish ? album.date_publish : album.date_create}}</li>
                            <li><b>Estado:</b>
                                <span v-if="album.status == 1">
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
    <div class="container" v-if="!loader && albums.length == 0" v-cloak>
        <h4>No hay paginas</h4>
    </div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Nueva Pagina" href="{{base_url('admin/paginas/nueva/')}}">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/AlbumsLists.min.js')}}"></script>
@endsection
