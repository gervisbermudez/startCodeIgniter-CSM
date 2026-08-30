<div v-show="sectionActive == 'general' || sectionActive == 'seo'">
    <div class="col s12 center" v-show="loader">
        <preloader />
    </div>
    <nav class="page-navbar" v-show="!loader && listedConfigurations.length > 0">
        <div class="nav-wrapper">
            <form @submit.prevent>
                <div class="input-field">
                    <input id="config-search" class="input-search" type="search" placeholder="<?php echo lang('search_placeholder'); ?>" v-model="filter" aria-label="<?php echo lang('search'); ?>">
                    <label class="label-icon" for="config-search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li>
                    <a href="#!" v-on:click.prevent="getconfigurations();" class="tooltipped" data-tooltip="<?php echo lang('search'); ?>" aria-label="<?php echo lang('search'); ?>">
                        <i class="material-icons">refresh</i>
                    </a>
                </li>
                <li>
                    <a href="#!" class="dropdown-trigger" data-target="dropdown-options-general" aria-label="<?php echo lang('options'); ?>">
                        <i class="material-icons">more_vert</i>
                    </a>
                    <ul id="dropdown-options-general" class="dropdown-content">
                        <li><a href="#!" v-on:click.prevent="changeSectionActive('addConfig')"><?php echo lang('config_add_entry'); ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="configurations" v-if="!loader && listedConfigurations.length > 0">
        <div class="row">
            <div class="col s12">
                <configuration v-for="configuration in listedConfigurations" :key="configuration.site_config_id" :configuration="configuration"></configuration>
            </div>
        </div>
    </div>
    <div class="config-empty" v-if="!loader && listedConfigurations.length == 0">
        <i class="material-icons" aria-hidden="true">settings</i>
        <p class="page-header"><?php echo lang('config_empty_title'); ?></p>
        <a href="#!" class="btn waves-effect waves-light btn-accent" v-on:click.prevent="changeSectionActive('addConfig')">
            <i class="material-icons left">add</i><?php echo lang('config_empty_cta'); ?>
        </a>
    </div>
</div>
