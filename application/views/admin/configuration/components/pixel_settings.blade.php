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
