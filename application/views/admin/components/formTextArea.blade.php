<script type="text/x-template" id="formFieldTextArea-template">
    <div class="row formFieldTextArea">
        <div class="col s12">
            <b>Field Preview:</b>
        </div>
        <div class="input-field col s12">
            <textarea :placeholder="fieldPlaceholder" v-if="configurable" v-model="fieldName" @keyup="convertfielApiID()" :id="fieldID" type="text" class="materialize-textarea"></textarea>
            <textarea :placeholder="fieldPlaceholder" v-else v-model="text" :id="fieldID" type="text" class="materialize-textarea"></textarea>
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
                  </div>
                </li>
              </ul>
        </div>
    </div>
</script>
