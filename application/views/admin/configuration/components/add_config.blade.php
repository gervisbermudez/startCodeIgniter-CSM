        <div v-show="sectionActive == 'addConfig'">
            <div class="config-section-header">
                <h2 class="page-header">{{ lang('config_add_entry') }}</h2>
            </div>
            <div class="card z-depth-1">
                <div class="card-content">
            <div class="row">
                <form class="col s12" @submit.prevent="saveNewConfig()">
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="new-config-name" v-model="newConfig.config_name" type="text" class="validate">
                            <label for="new-config-name">{{ lang('config_name') }}</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="new-config-label" v-model="newConfig.config_label" type="text" class="validate">
                            <label for="new-config-label">{{ lang('config_label') }}</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="new-config-value" v-model="newConfig.config_value" type="text" class="validate">
                            <label for="new-config-value">{{ lang('config_value') }}</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="new-config-description" v-model="newConfig.config_description" type="text" class="validate">
                            <label for="new-config-description">{{ lang('description') }}</label>
                        </div>
                        <div class="input-field col s12">
                            <select id="new-config-type" name="config_type" v-model="newConfig.config_type">
                                <option value="" disabled>{{ lang('config_choose_option') }}</option>
                                <option value="general">{{ lang('config_general') }}</option>
                                <option value="seo">{{ lang('config_seo') }}</option>
                                <option value="integrations">{{ lang('config_integrations') }}</option>
                                <option value="system">{{ lang('config_system') }}</option>
                            </select>
                            <label for="new-config-type">{{ lang('config_type') }}</label>
                        </div>
                        <div class="input-field col s12">
                            <div class="switch">
                                <label>
                                    {{ lang('config_not_active') }}
                                    <input type="checkbox" name="visible_form" value="1" v-model="newConfig.status">
                                    <span class="lever"></span>
                                    {{ lang('config_active') }}
                                </label>
                            </div>
                        </div>
                        <div class="col s12 config-actions">
                            <button type="button" class="btn-flat" @click="changeSectionActive('general')">{{ lang('cancel') }}</button>
                            <button type="submit" class="btn waves-effect waves-light btn-accent">
                                <i class="material-icons left">save</i> {{ lang('save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
                </div>
            </div>
        </div>
