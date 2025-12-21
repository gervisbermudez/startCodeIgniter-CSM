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
                                    <input v-model="newConfig.config_label" placeholder="Config Label" type="text"
                                        class="validate">
                                    <label>Config Label (Human Friendly)</label>
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
