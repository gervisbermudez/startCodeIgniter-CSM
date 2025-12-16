@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection

@section('head_includes')

@endsection

@section('content')
<div id="root" class="configuration-root">
    <transition>
        <div class="row" v-show="sectionActive == 'home'">
            <div class="col s12 m3">
                <div v-on:click="changeSectionActive('general')" class="config-card card-panel blue">
                    <div class="row">
                        <div class="col s5">
                            <div class="div">
                                <i class="material-icons medium white-text">build</i>
                            </div>
                            <div class="white-text">
                                General
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
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
                <div v-on:click="changeSectionActive('theme')" class="config-card card-panel pink">
                    <div class="row">
                        <div class="col s5">
                            <div class="div">
                                <i class="material-icons medium white-text">brush</i>
                            </div>
                            <div class="white-text">
                                Theme
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
                            <div class="info white-text right-align">
                                @{{themeConfigurations.length}}
                                <br />
                                Configuraciones
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="config-card card-panel teal">
                    <div class="row">
                        <div class="col s5" v-on:click="changeSectionActive('analytics')">
                            <div class="div">
                                <i class="material-icons medium white-text">insert_chart</i>
                            </div>
                            <div class="white-text">
                                Analytics
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
                            <div class="info white-text right-align">
                                Seguimiento <br />
                                <div class="switch">
                                    <label>
                                        Off
                                        <input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')"
                                            v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')">
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
                        <div class="col s5">
                            <div class="div">
                                <i class="material-icons medium white-text">trending_up</i>
                            </div>
                            <div class="white-text">
                                SEO
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
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
                <div class="config-card card-panel deep-purple">
                    <div class="row">
                        <div class="col s5" v-on:click="changeSectionActive('pixel')">
                            <div class="div">
                                <i class="material-icons medium white-text">insert_chart</i>
                            </div>
                            <div class="white-text">
                                Pixel Facebook
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
                            <div class="info white-text right-align">
                                Seguimiento <br />
                                <div class="switch">
                                    <label>
                                        Off
                                        <input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')"
                                            v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')">
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
                <div class="config-card card-panel amber">
                    <div class="row">
                        <div class="col s5" v-on:click="changeSectionActive('database')">
                            <div class="div">
                                <i class="material-icons medium white-text">sd_card</i>
                            </div>
                            <div class="white-text">
                                Database
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
                            <div class="info white-text right-align">
                                @{{files.length}}
                                <br />
                                Create Backup
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div v-on:click="changeSectionActive('updater')" class="config-card card-panel red">
                    <div class="row">
                        <div class="col s5">
                            <div class="div">
                                <i class="material-icons medium white-text">system_update_alt</i>
                            </div>
                            <div class="white-text">
                                Updater
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
                            <div class="info white-text right-align">
                                Manual Check <br />
                                <div class="switch">
                                    <label>
                                        Off
                                        <input type="checkbox" :checked="getConfigValueBoolean('UPDATER_MANUAL_CHECK')"
                                            v-on:change="updateConfigCheckbox($event, 'UPDATER_MANUAL_CHECK')">
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
                <div class="config-card card-panel indigo">
                    <div class="row">
                        <div class="col s5" v-on:click="changeSectionActive('logger')">
                            <div class="div">
                                <i class="material-icons medium white-text">list</i>
                            </div>
                            <div class="white-text">
                                Logger
                            </div>
                        </div>
                        <div class="col s7 tooltipped" data-position="bottom" data-tooltip="Ver más">
                            <div class="info white-text right-align">
                                System logger <br />
                                <div class="switch">
                                    <label>
                                        Off
                                        <input type="checkbox" :checked="getConfigValueBoolean('SYSTEM_LOGGER')"
                                            v-on:change="updateConfigCheckbox($event, 'SYSTEM_LOGGER')">
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
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'analytics'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Google Analytics</h4>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p>
                        <label>
                            <span>Activar seguimiento</span>
                        </label>
                    <div class="switch">
                        <label>
                            Off
                            <input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')"
                                v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                    </p>
                    <!-- Switch -->
                    <div class="row">
                        <div class="input-field col s6">
                            <input :value="getConfigValue('ANALYTICS_ID')" placeholder="UA-XXXXX-Y" type="text"
                                class="validate" v-on:change="updateConfig($event, 'ANALYTICS_ID')">
                            <label class="active">ID de seguimiento de GA</label>
                        </div>
                    </div>
                    <div class="row">
                        <form class="col s12">
                            <div class="row">
                                <div class="input-field col s6">
                                    <input :value="getConfigValue('ANALYTICS_CODE')" placeholder="" type="text"
                                        class="validate" v-on:change="updateConfig($event, 'ANALYTICS_CODE')">
                                    <label>Head Code</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'addConfig'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Agregar Entrada de Configuracion</h4>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="row">
                        <form class="col s12">
                            <div class="row">
                                <div class="input-field col s6">
                                    <input v-model="newConfig.config_name" placeholder="Config Name" type="text"
                                        class="validate">
                                    <label>Config Name</label>
                                </div>
                                <div class="input-field col s6">
                                    <input v-model="newConfig.config_value" placeholder="Config Value" type="text"
                                        class="validate">
                                    <label>Config Value</label>
                                </div>
                                <div class="input-field col s12">
                                    <input v-model="newConfig.config_description" placeholder="Config Description"
                                        type="text" class="validate">
                                    <label>Config Description</label>
                                </div>
                                <div class="input-field col s12">
                                    <select name="config_type" v-model="newConfig.config_type">
                                        <option value="" disabled selected>Choose your option</option>
                                        <option value="general">general</option>
                                        <option value="seo">seo</option>
                                        <option value="theme">theme</option>
                                        <option value="analytics">analytics</option>
                                        <option value="updater">updater</option>
                                        <option value="logger">logger</option>
                                    </select>
                                    <label>Config Type</label>
                                </div>
                                <div class="input-field col s12">
                                    Activar
                                    <div class="switch">
                                        <label>
                                            No activo
                                            <input type="checkbox" name="visible_form" value="1"
                                                v-model="newConfig.status">
                                            <span class="lever"></span>
                                            Activo
                                        </label>
                                    </div>
                                </div>
                                <div class="input-field col s12">
                                    <button type="button" class="btn btn-primary" @click="saveNewConfig()">
                                        <span><i class="material-icons right">edit</i> Guardar</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'pixel'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Facebook Pixel</h4>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p>
                        <label>
                            <span>Activar seguimiento</span>
                        </label>
                    <div class="switch">
                        <label>
                            Off
                            <input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')"
                                v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                    </p>
                    <div class="row">
                        <form class="col s12">
                            <div class="row">
                                <div class="input-field col s6">
                                    <input :value="getConfigValue('PIXEL_CODE')" placeholder="" type="text"
                                        class="validate" v-on:change="updateConfig($event, 'PIXEL_CODE')">
                                    <label>Head Code</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'seo'">
            <div class="col s12 center" v-bind:class="{ hide: !loader }">
                <br><br>
                <preloader />
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
                                            :href="base_url('admin/users/ver/' + configuration.user_id)">@{{configuration.user.get_fullname()}}</a>
                                    </td>
                                    <td>
                                        @{{configuration.date_publish ? configuration.date_publish : configuration.date_create}}
                                    </td>
                                    <td>
                                        <i v-if="configuration.status == 1" class="material-icons tooltipped"
                                            data-position="bottom" data-delay="50" data-tooltip="Publicado">publish</i>
                                        <i v-else class="material-icons tooltipped" data-position="bottom"
                                            data-delay="50" data-tooltip="Borrador">edit</i>
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
                <preloader />
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
                            <a href="#!" class='dropdown-trigger' data-target='dropdown-options-general'><i
                                    class="material-icons">more_vert</i></a>
                            <!-- Dropdown Structure -->
                            <ul id='dropdown-options-general' class='dropdown-content'>
                                <li><a href="#!" v-on:click="changeSectionActive('addConfig')">Add</a></li>
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
                                            :href="base_url('admin/users/ver/' + configuration.user_id)">@{{configuration.user.get_fullname()}}</a>
                                    </td>
                                    <td>
                                        @{{configuration.date_publish ? configuration.date_publish : configuration.date_create}}
                                    </td>
                                    <td>
                                        <i v-if="configuration.status == 1" class="material-icons tooltipped"
                                            data-position="bottom" data-delay="50" data-tooltip="Publicado">publish</i>
                                        <i v-else class="material-icons tooltipped" data-position="bottom"
                                            data-delay="50" data-tooltip="Borrador">edit</i>
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
    <transition name="fade">
        <div v-show="sectionActive == 'database'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Manage database backup</h4>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p>
                    <h5>Crear backup</h5>
                    <a class="waves-effect waves-light btn amber" @click="createDatabaseBackup()"><i
                            class="material-icons left">sd_card</i> backup</a>
                    </p>
                    <p v-if="files.length">
                        Backups creados:
                    </p>
                    <ul class="collapsible" v-if="files.length">
                        <li v-for="(file, index) in files" :key="index">
                            <div class="collapsible-header"><i class="material-icons">filter_drama</i>
                                @{{file.get_filename()}}
                            </div>
                            <div class="collapsible-body">
                                <ul>
                                    <li>
                                        <b>Fecha:</b> @{{file.date_create}}
                                    </li>
                                    <li>
                                        <b>File Type:</b> @{{file.file_type}}
                                    </li>
                                    <li>
                                        <b>Path:</b> @{{file.file_path}}
                                    </li>
                                </ul>
                                <br />
                                <a class="waves-effect waves-light btn amber" :href="file.get_full_file_path()"><i
                                        class="material-icons left">file_download</i> Download</a>
                                <a class="waves-effect waves-light btn red" @click="deleteFile(file);"><i
                                        class="material-icons left">delete</i> Delete</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'theme'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Manage Themes</h4>
                </div>
            </div>
            <div class="row pages">
                <div class="col s12">
                    <div class="col s12 m4" v-for="(theme, index) in themes" :key="index">
                        <div class="card page-card">
                            <div class="card-image">
                                <div class="card-image-container">
                                    <img :src="getThemePreviewUrl(index, theme)" />
                                </div>
                                <label class="indicator">
                                    <input name="group1" type="radio" :checked="getThemeIsChecked(index)"
                                        v-on:change="changeTheme(index)" />
                                    <span>&nbsp;</span>
                                </label>
                            </div>
                            <div class="card-content">
                                <div>
                                    <span class="card-title">@{{theme.name}}
                                        <a href="#!"><span class="activator right"><i
                                                    class="material-icons">more_vert</i></span></a>
                                    </span>
                                    <div class="card-info">
                                        <p>
                                            @{{theme.description}}
                                        </p>
                                        <a @click="changeTheme(index)" class="waves-effect waves-light btn "><i
                                                class="material-icons left">brush</i>Aplicar theme</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-reveal">
                                <span class="card-title grey-text text-darken-4">
                                    <i class="material-icons right">close</i>
                                    @{{theme.name}}
                                </span>
                                <span class="subtitle">
                                    @{{theme.description}}
                                </span>
                                <ul>
                                    <li><b>Author:</b> <br> @{{theme.author}}</li>
                                    <li><b>Actualizacion:</b> <br> @{{theme.updated}}</li>
                                    <li><b>License:</b> @{{theme.license}}</li>
                                    <li><b>Url:</b> @{{theme.url}}</li>
                                    <li><b>Url:</b> @{{theme.url}}</li>
                                    <li><b>Version:</b> @{{theme.version}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'updater'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Updater Manager</h4>
                </div>
            </div>
            <div class="row" v-if="getConfig('UPDATER_LAST_CHECK_UPDATE')">
                <div class="col s12">
                    <p>
                        <b>Last check</b>: @{{getConfig('UPDATER_LAST_CHECK_UPDATE').config_value}}
                    </p>
                    <div v-if="updaterInfo">
                        <div class="subtitle">
                            Current Start CMS Version:
                        </div>
                        <ul class="collection">
                            <li class="collection-item"><b>Name</b>: @{{updaterInfo.local.name}}</li>
                            <li class="collection-item"><b>Description</b>: @{{updaterInfo.local.description}}</li>
                            <li class="collection-item"><b>Version</b>: @{{updaterInfo.local.version}}</li>
                            <li class="collection-item"><b>Updated</b>: @{{updaterInfo.local.updated}}</li>
                            <li class="collection-item"><b>Url</b>: @{{updaterInfo.local.url}}</li>
                        </ul>
                        <br />
                    </div>
                    <div v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version)">
                        <div class="subtitle">
                            Available Start CMS Version:
                        </div>
                        <ul class="collection">
                            <li class="collection-item"><b>Name</b>: @{{updaterInfo.remote.name}}</li>
                            <li class="collection-item"><b>Description</b>: @{{updaterInfo.remote.description}}</li>
                            <li class="collection-item"><b>Version</b>: @{{updaterInfo.remote.version}}</li>
                            <li class="collection-item"><b>Updated</b>: @{{updaterInfo.remote.updated}}</li>
                            <li class="collection-item"><b>Url</b>: @{{updaterInfo.remote.url}}</li>
                        </ul>
                        <br />
                    </div>
                    <div v-if="updaterInfo && (updaterInfo.remote.version <= updaterInfo.local.version)">
                        You have the last version of Start CMS!
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 center" v-bind:class="{ hide: !updaterloader }">
                    Checking Updates...
                    <br><br>
                    <preloader />
                </div>
                <div class="col s12" v-if="!updaterloader && !updaterInfo">
                    <p>
                        <a class="waves-effect waves-light btn" @click="checkUpdates()"><i
                                class="material-icons left">sync</i> Check for updates</a>
                    </p>
                </div>
                <div class="col s12"
                    v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version) && !updaterPackageDownloaded">
                    <div class="download-progress center-align" v-if="updaterProgress">
                        Downloading package...
                        <br />
                        <br />
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <p v-if="!updaterProgress">
                        <a class="waves-effect waves-light btn" @click="downloadUpdateVersion()"><i
                                class="material-icons left">file_download</i> Download Package</a>
                    </p>
                </div>
                <div class="col s12"
                    v-if="updaterInfo && (updaterInfo.remote.version > updaterInfo.local.version) && updaterPackageDownloaded">
                    <div class="download-progress center-align" v-if="updaterInstallProgress">
                        Installing package in progress...
                        <br />
                        <br />
                        <div class="progress">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <p v-if="!updaterInstallProgress">
                        <a class="waves-effect waves-light btn" @click="installDownloadedPackage()"><i
                                class="material-icons left">system_update_alt</i> Install Package</a>
                    </p>
                </div>
            </div>
        </div>
    </transition>
    <transition name="fade">
        <div v-show="sectionActive == 'logger'">
            <div class="col s12 center" v-bind:class="{ hide: !loader }">
                <br><br>
                <preloader />
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
                                <tr v-for="(configuration, index) in loggerConfig" :key="index">
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
                                            :href="base_url('admin/users/ver/' + configuration.user_id)">@{{configuration.user.get_fullname()}}</a>
                                    </td>
                                    <td>
                                        @{{configuration.date_publish ? configuration.date_publish : configuration.date_create}}
                                    </td>
                                    <td>
                                        <i v-if="configuration.status == 1" class="material-icons tooltipped"
                                            data-position="bottom" data-delay="50" data-tooltip="Publicado">publish</i>
                                        <i v-else class="material-icons tooltipped" data-position="bottom"
                                            data-delay="50" data-tooltip="Borrador">edit</i>
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
                        <configuration v-for="(configuration, index) in loggerConfig" :key="index"
                            :configuration="configuration"></configuration>
                    </div>
                </div>
            </div>
            <div class="container" v-if="!loader && configurations.length == 0" v-cloak>
                <h4>No hay Configuraciones</h4>
            </div>
        </div>
    </transition>
    @if(has_permisions('CREATE_CONFIG'))
    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
            data-delay="50" data-tooltip="Agregar configuracion"
            href="<?php echo base_url('admin/configuration/new/') ?>">
            <i class="large material-icons">add</i>
        </a>
    </div>
    @endif
</div>
@include('admin.components.configurationComponent')
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/validateForm.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/configurationComponent.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/ConfiguracionList.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection