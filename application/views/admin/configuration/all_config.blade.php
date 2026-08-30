@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <aside class="col s12 m3 l2 config-sidebar hide-on-small-only">
            <div class="collection">
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'home'}" @click.prevent="changeSectionActive('home')">
                    <i class="material-icons" aria-hidden="true">dashboard</i>
                    <span>{{ lang('dashboard_overview') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'general'}" @click.prevent="changeSectionActive('general')">
                    <i class="material-icons" aria-hidden="true">tune</i>
                    <span>{{ lang('config_general') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'theme'}" @click.prevent="changeSectionActive('theme')">
                    <i class="material-icons" aria-hidden="true">palette</i>
                    <span>{{ lang('config_appearance') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'seo'}" @click.prevent="changeSectionActive('seo')">
                    <i class="material-icons" aria-hidden="true">search</i>
                    <span>{{ lang('config_seo') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'integrations'}" @click.prevent="changeSectionActive('integrations')">
                    <i class="material-icons" aria-hidden="true">extension</i>
                    <span>{{ lang('config_integrations') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'system'}" @click.prevent="changeSectionActive('system')">
                    <i class="material-icons" aria-hidden="true">build</i>
                    <span>{{ lang('config_system') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'updater'}" @click.prevent="changeSectionActive('updater')">
                    <i class="material-icons" aria-hidden="true">system_update</i>
                    <span>{{ lang('config_updates') }}</span>
                </a>
            </div>
        </aside>

        <div class="col s12 m9 l10 config-content">
            <div class="hide-on-med-and-up config-mobile-nav">
                <div class="input-field">
                    <select v-model="sectionActive" @change="changeSectionActive(sectionActive)">
                        <option value="home">{{ lang('dashboard_overview') }}</option>
                        <option value="general">{{ lang('config_general') }}</option>
                        <option value="theme">{{ lang('config_appearance') }}</option>
                        <option value="seo">{{ lang('config_seo') }}</option>
                        <option value="integrations">{{ lang('config_integrations') }}</option>
                        <option value="system">{{ lang('config_system') }}</option>
                        <option value="updater">{{ lang('config_updates') }}</option>
                    </select>
                    <label>{{ lang('menu_configuration') }}</label>
                </div>
            </div>

            @include('admin.configuration.components.home_dashboard')
            @include('admin.configuration.components.general_settings')
            @include('admin.configuration.components.analytics_settings')
            @include('admin.configuration.components.add_config')
            @include('admin.configuration.components.theme_selector')
            @include('admin.configuration.components.updater_manager')
            @include('admin.configuration.components.system_settings')
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.configuration.components.i18n')
@include('admin.components.configuration_component')
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ConfigurationComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ConfigurationList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
