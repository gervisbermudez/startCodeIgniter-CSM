<script type="text/x-template" id="formFieldBoolean-template">
    <div class="row formFieldBoolean">
        <div class="col s12">
            <b>Field Preview:</b>
        </div>
        <div class="input-field col s12">
            <p v-for="(checkbox, index) in checkboxes" :key="index">
                <label>
                    <input type="checkbox" :checked="checkbox.checked" v-model="checkbox.checked"/>
                    <span>@{{checkbox.label}}</span>
                </label>
            </p>
        </div>
        <div class="col s12" v-if="configurable">
            <ul class="collapsible">
                <li>
                  <div class="collapsible-header"><i class="material-icons">settings</i>Config</div>
                  <div class="collapsible-body">
                    <div class="row">
                        <div class="input-field col s12">
                            Field Name
                            <br />
                            <input placeholder="Field Name" @keyup="convertfielApiID()" v-model="fieldName" type="text" class="validate">
                        </div>
                        <div class="input-field col s12">
                            Api ID
                            <input placeholder="Api ID" v-model="fielApiID" type="text" class="validate">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <div class="title grey-text text-darken-2">
                            <i class="material-icons left">layers</i> Options
                            <a class="waves-effect waves-light btn right" href="#!" @click="addOption();">Agregar Option +</a>
                            </div>
                            <br />
                            <ul class="collapsible" :id="'collapsible' + fieldID" style="poasition: relative">
                                <li v-for="(checkbox, index) in checkboxes">
                                    <div class="collapsible-header">
                                        <input placeholder="name" v-model="checkbox.label" type="text" class="validate">
                                        <i class="material-icons right remove" @click="removeOption(index);">do_not_disturb_on</i>
                                    </div>
                                    <div class="collapsible-body">
                                        <span v-if="checkbox">Value:</span>
                                        <div class="input-field">
                                            <input placeholder="content" v-model="checkbox.checked" type="text" class="validate">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                  </div>
                </li>
              </ul>
        </div>
    </div>
</script>