        <div v-show="sectionActive == 'integrations'">
            <div class="config-section-header">
                <h2 class="page-header"><?= lang('config_integrations') ?></h2>
                <p class="section-description"><?= lang('config_integrations_desc') ?></p>
            </div>

            <div class="card z-depth-1 integrations-card">
                <div class="card-content">
                    <span class="widget-title">
                        <i class="material-icons" aria-hidden="true">insert_chart</i> <?= lang('google_analytics') ?>
                    </span>
                    <p>
                        <label><?= lang('activate_tracking') ?></label>
                        <div class="switch">
                            <label>
                                <?= lang('off') ?>
                                <input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')"
                                    v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')">
                                <span class="lever"></span>
                                <?= lang('on') ?>
                            </label>
                        </div>
                    </p>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="analytics-id" :value="getConfigValue('ANALYTICS_ID')" placeholder="<?= lang('analytics_ga4_placeholder') ?>" type="text"
                                class="validate" v-on:change="updateConfig($event, 'ANALYTICS_ID')">
                            <label for="analytics-id" class="active"><?= lang('ga_tracking_id') ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="analytics-code" :value="getConfigValue('ANALYTICS_CODE')" class="materialize-textarea"
                                v-on:change="updateConfig($event, 'ANALYTICS_CODE')"
                                placeholder="<!-- gtag snippet -->"></textarea>
                            <label for="analytics-code" class="active"><?= lang('analytics_head_code') ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card z-depth-1 integrations-card">
                <div class="card-content">
                    <span class="widget-title">
                        <i class="material-icons" aria-hidden="true">code</i> <?= lang('config_facebook_pixel') ?>
                    </span>
                    <p>
                        <label><?= lang('activate_tracking') ?></label>
                        <div class="switch">
                            <label>
                                <?= lang('off') ?>
                                <input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')"
                                    v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')">
                                <span class="lever"></span>
                                <?= lang('on') ?>
                            </label>
                        </div>
                    </p>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="pixel-code" :value="getConfigValue('PIXEL_CODE')" type="text"
                                class="validate" v-on:change="updateConfig($event, 'PIXEL_CODE')">
                            <label for="pixel-code" class="active"><?= lang('config_pixel_head') ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card z-depth-1 integrations-card">
                <div class="card-content">
                    <span class="widget-title">
                        <i class="material-icons" aria-hidden="true">visibility</i> <?= lang('config_visitor_tracking') ?>
                    </span>
                    <p class="section-description"><?= lang('config_visitor_tracking_desc') ?></p>
                    <p>
                        <label><?= lang('activate_tracking') ?></label>
                        <div class="switch">
                            <label>
                                <?= lang('off') ?>
                                <input type="checkbox" :checked="getConfigValueBoolean('SITEM_TRACK_VISITORS')"
                                    v-on:change="updateConfigCheckbox($event, 'SITEM_TRACK_VISITORS')">
                                <span class="lever"></span>
                                <?= lang('on') ?>
                            </label>
                        </div>
                    </p>
                </div>
            </div>
        </div>
