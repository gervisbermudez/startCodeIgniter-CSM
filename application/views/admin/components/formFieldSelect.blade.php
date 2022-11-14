<script type="text/x-template" id="formFieldselect-template">
    <div class="row formFieldselect">
        <div class="col s12">
            <b>Select Preview:</b>
        </div>
        <div class="input-field col s12">
            <div class="input-field">
                    <select name="usergroup_id" v-model="selectValue" :id="fieldID">
                        <option v-for="(item, index) in selectOptions" :key="index" :value="item.value">
                            @{{item.label}}</option>
                    </select>
                    <label :for="fieldID">@{{fieldName}}</label>
            </div>
        </div>
        <div class="col s12" v-if="configurable">
            <ul class="collapsible">
                <li>
                  <div class="collapsible-header"><i class="material-icons">settings</i>Config</div>
                  <div class="collapsible-body">
                    <div class="row">
                        <div class="input-field col s12">
                            Select Name
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
                            <i class="material-icons left">layers</i> Select Options
                            <a class="waves-effect waves-light btn right" href="#!" @click="addOption();">Agregar Option +</a>
                            </div>
                            <br />
                            <ul class="collapsible" :id="'collapsible' + fieldID" style="poasition: relative">
                                <li v-for="(option, index) in selectOptions">
                                    <div class="collapsible-header">
                                        <input placeholder="name" v-model="option.label" type="text" class="validate" v-on:change="setOption(option);">
                                        <i class="material-icons right remove" @click="removeOption(index);">do_not_disturb_on</i>
                                    </div>
                                    <div class="collapsible-body">
                                        <span v-if="option">Value:</span>
                                        <div class="input-field">
                                            <input placeholder="content" v-model="option.value"  v-on:change="updateSelect()" type="text" class="validate">
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