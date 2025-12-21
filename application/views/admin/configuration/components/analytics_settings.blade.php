        <div v-show="sectionActive == 'analytics'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4><?= lang('google_analytics') ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p>
                        <label>
                            <span><?= lang('activate_tracking') ?></span>
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
                            <label class="active"><?= lang('ga_tracking_id') ?></label>
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
