<script type="text/x-template" id="formFieldNumber-template">
    <div class="row formFieldNumber">
        <div class="col s12">
            <b>Field Preview:</b>
        </div>
        <div class="input-field col s12">
            <input :placeholder="fieldPlaceholder" v-if="configurable" v-model="fieldName" :id="fieldID" type="number" class="validate">
            <input :placeholder="fieldPlaceholder" v-else v-model="number" :id="fieldID" type="number" class="validate">
            <label :for="fieldID" class="active">@{{fieldName}}</label>
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
                        <div class="input-field col s12">
                            Placeholder
                            <input placeholder="Field Placeholder" v-model="fieldPlaceholder" type="text" class="validate">
                        </div>
                    </div>
                  </div>
                </li>
              </ul>
        </div>
    </div>
</script>
