<div v-show="sectionActive == 'general' || sectionActive == 'seo' || sectionActive == 'logger'">
            <div class="col s12 center" v-bind:class="{ hide: !loader }">
                <br><br>
                <preloader />
            </div>
            <nav class="page-navbar" v-show="!loader && configurations.length > 0">
                <div class="nav-wrapper">
                    <form>
                        <div class="input-field">
                            <input class="input-search" type="search" placeholder="<?php echo lang('search'); ?>..." v-model="filter">
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
            <div class="configurations" v-if="!loader && configurations.length > 0">
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
                                <tr v-for="(configuration, index) in (sectionActive == 'seo' ? seoConfigurations : (sectionActive == 'logger' ? loggerConfig : generalConfigurations))" :key="index">
                                    <td>@{{configuration.config_label || configuration.config_name}}</td>
                                    <td>
                                        <div class="input-field" v-if="configuration.editable">
                                            <input type="text" class="validate"
                                                v-model="configuration.config_value"
                                                v-on:blur="saveConfig(configuration);"
                                                v-on:focus="focusInput(configuration);">
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
                                            <li><a href="#!" v-on:click="toggleEddit(configuration);"><?php echo lang('edit'); ?></a></li>
                                            <li><a href="#!" v-on:click="deletePage(configuration, index);"><?php echo lang('delete'); ?></a>
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
                        <configuration v-for="(configuration, index) in (sectionActive == 'seo' ? seoConfigurations : (sectionActive == 'logger' ? loggerConfig : generalConfigurations))" :key="index"
                            :configuration="configuration"></configuration>
                    </div>
                </div>
            </div>
            <div class="container" v-if="!loader && configurations.length == 0">
                <h4><?php echo lang('no_configurations'); ?></h4>
            </div>
        </div>
