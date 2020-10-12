@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root" class="configuration-root">
    <div class="row" v-show="sectionActive == 'home'">
        <div class="col s12 m3">
            <div v-on:click="changeSectionActive('general')" class="config-card card-panel blue">
                <div class="row">
                    <div class="col s6">
                        <div class="div">
                            <i class="material-icons medium white-text">build</i>
                        </div>
                        <div class="white-text">
                            General
                        </div>
                    </div>
                    <div class="col s6 tooltipped" data-position="bottom" data-tooltip="Ver más">
                        <div class="info white-text right-align">
                            @{{generalConfigurations.length}} 
                            <br />
                            Configuraciones
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m3">
            <div v-on:click="changeSectionActive('analytics')" class="config-card card-panel teal">
                <div class="row">
                    <div class="col s6">
                        <div class="div">
                            <i class="material-icons medium white-text">insert_chart</i>
                        </div>
                        <div class="white-text">
                            Analytics
                        </div>
                    </div>
                    <div class="col s6 tooltipped" data-position="bottom" data-tooltip="Ver más">
                        <div class="info white-text right-align">
                            Seguimiento <br/>
                            <div class="switch">
                                <label>
                                    Off
                                    <input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')">
                                    <span class="lever"></span>
                                    On
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m3">
            <div v-on:click="changeSectionActive('seo')" class="config-card card-panel green">
                <div class="row">
                    <div class="col s6">
                        <div class="div">
                            <i class="material-icons medium white-text">trending_up</i>
                        </div>
                        <div class="white-text">
                            SEO
                        </div>
                    </div>
                    <div class="col s6 tooltipped" data-position="bottom" data-tooltip="Ver más">
                        <div class="info white-text right-align">
                            @{{generalConfigurations.length}} 
                            <br />
                            Configuraciones
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12 m3">
            <div v-on:click="changeSectionActive('pixel')" class="config-card card-panel deep-purple">
                <div class="row">
                    <div class="col s6">
                        <div class="div">
                            <i class="material-icons medium white-text">insert_chart</i>
                        </div>
                        <div class="white-text">
                            Pixel Facebook
                        </div>
                    </div>
                    <div class="col s6 tooltipped" data-position="bottom" data-tooltip="Ver más">
                        <div class="info white-text right-align">
                            Seguimiento <br/>
                            <div class="switch">
                                <label>
                                    Off
                                    <input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')">
                                    <span class="lever"></span>
                                    On
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <transition name="fade">
        <div v-show="sectionActive == 'analytics'" class="container">
            <h2>Google Analytics</h2>
            <p>
                <label>
                    <span>Activar seguimiento</span>
                </label>
            <div class="switch">
                <label>
                    Off
                    <input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')">
                    <span class="lever"></span>
                    On
                </label>
            </div>
            </p>
            <!-- Switch -->
            <div class="row">
                <div class="input-field col s6">
                    <input :value="getConfigValue('ANALYTICS_ID')" placeholder="UA-XXXXX-Y" type="text" class="validate"
                        v-on:change="updateConfig($event, 'ANALYTICS_ID')">
                    <label class="active" >ID de seguimiento de GA</label>
                </div>
            </div>
            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s6">
                            <input :value="getConfigValue('ANALYTICS_CODE')"  placeholder="" type="text" class="validate" v-on:change="updateConfig($event, 'ANALYTICS_CODE')">
                            <label>Head Code</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'pixel'" class="container">
            <h2>Facebook Pixel</h2>
            <p>
                <label>
                    <span>Activar seguimiento</span>
                </label>
            <div class="switch">
                <label>
                    Off
                    <input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')">
                    <span class="lever"></span>
                    On
                </label>
            </div>
            </p>
            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s6">
                            <input :value="getConfigValue('PIXEL_CODE')"  placeholder="" type="text" class="validate" v-on:change="updateConfig($event, 'PIXEL_CODE')">
                            <label>Head Code</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'seo'">
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
            <nav class="page-navbar" v-cloak v-show="!loader && configurations.length > 0">
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
                            <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i
                                    class="material-icons">more_vert</i></a>
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
            <div class="configurations" v-cloak v-if="!loader && configurations.length > 0">
                <div class="row" v-if="tableView">
                    <div class="col s12">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Current Value</th>
                                    <th>Categorie</th>
                                    <th>Author</th>
                                    <th>Publish Date</th>
                                    <th>Status</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(configuration, index) in seoConfigurations" :key="index">
                                    <td>@{{configuration.config_name}}</td>
                                    <td>
                                        <div class="input-field" v-if="configuration.editable">
                                            <input id="last_name" type="text" class="validate"
                                                v-model="configuration.config_value"
                                                v-on:blur="saveConfig(configuration);">
                                        </div>
                                        <div v-else>
                                            @{{configuration.config_value}}
                                        </div>
                                    </td>
                                    <td>@{{configuration.config_type}}</td>
                                    <td><a
                                            :href="base_url('admin/usuarios/ver/' + configuration.user_id)">@{{configuration.user.get_fullname()}}</a>
                                    </td>
                                    <td>
                                        @{{configuration.date_publish ? configuration.date_publish : configuration.date_create}}
                                    </td>
                                    <td>
                                        <i v-if="configuration.status == 1" class="material-icons tooltipped"
                                            data-position="bottom" data-delay="50" data-tooltip="Publicado">publish</i>
                                        <i v-else class="material-icons tooltipped" data-position="bottom" data-delay="50"
                                            data-tooltip="Borrador">edit</i>
                                    </td>
                                    <td>
                                        <a class='dropdown-trigger' href='#!'
                                            :data-target='"dropdown" + configuration.site_config_id'><i
                                                class="material-icons">more_vert</i></a>
                                        <ul :id='"dropdown" + configuration.site_config_id' class='dropdown-content'>
                                            <li><a href="#!" v-on:click="toggleEddit(configuration);">Editar</a></li>
                                            <li><a href="#!" v-on:click="deletePage(configuration, index);">Borrar</a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col s12">
                        <configuration v-for="(configuration, index) in seoConfigurations" :key="index"
                            :configuration="configuration"></configuration>
                    </div>
                </div>
            </div>
            <div class="container" v-if="!loader && configurations.length == 0" v-cloak>
                <h4>No hay Configuraciones</h4>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'general'">
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
            <nav class="page-navbar" v-cloak v-show="!loader && configurations.length > 0">
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
                            <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i
                                    class="material-icons">more_vert</i></a>
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
            <div class="configurations" v-cloak v-if="!loader && configurations.length > 0">
                <div class="row" v-if="tableView">
                    <div class="col s12">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Current Value</th>
                                    <th>Categorie</th>
                                    <th>Author</th>
                                    <th>Publish Date</th>
                                    <th>Status</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(configuration, index) in generalConfigurations" :key="index">
                                    <td>@{{configuration.config_name}}</td>
                                    <td>
                                        <div class="input-field" v-if="configuration.editable">
                                            <input id="last_name" type="text" class="validate"
                                                v-model="configuration.config_value"
                                                v-on:blur="saveConfig(configuration);">
                                        </div>
                                        <div v-else>
                                            @{{configuration.config_value}}
                                        </div>
                                    </td>
                                    <td>@{{configuration.config_type}}</td>
                                    <td><a
                                            :href="base_url('admin/usuarios/ver/' + configuration.user_id)">@{{configuration.user.get_fullname()}}</a>
                                    </td>
                                    <td>
                                        @{{configuration.date_publish ? configuration.date_publish : configuration.date_create}}
                                    </td>
                                    <td>
                                        <i v-if="configuration.status == 1" class="material-icons tooltipped"
                                            data-position="bottom" data-delay="50" data-tooltip="Publicado">publish</i>
                                        <i v-else class="material-icons tooltipped" data-position="bottom" data-delay="50"
                                            data-tooltip="Borrador">edit</i>
                                    </td>
                                    <td>
                                        <a class='dropdown-trigger' href='#!'
                                            :data-target='"dropdown" + configuration.site_config_id'><i
                                                class="material-icons">more_vert</i></a>
                                        <ul :id='"dropdown" + configuration.site_config_id' class='dropdown-content'>
                                            <li><a href="#!" v-on:click="toggleEddit(configuration);">Editar</a></li>
                                            <li><a href="#!" v-on:click="deletePage(configuration, index);">Borrar</a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col s12">
                        <configuration v-for="(configuration, index) in generalConfigurations" :key="index"
                            :configuration="configuration"></configuration>
                    </div>
                </div>
            </div>
            <div class="container" v-if="!loader && configurations.length == 0" v-cloak>
                <h4>No hay Configuraciones</h4>
            </div>
        </div>
    </transition>
</div>
@include('admin.components.configurationcomponent')
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/validateForm.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/configurationComponent.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/ConfiguracionList.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection