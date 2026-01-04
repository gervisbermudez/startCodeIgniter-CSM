<script type="text/x-template" id="formTextFormat-template">
    <div class="row formTextFormat">
    <div class="col s12" v-if="configurable">
          <b>Field Preview:</b>
      </div>
        <div class="col s12" v-else>
        <b>@{{fieldName}}</b>
        </div>
        <div class="input-field col s12">
            <textarea :id="fieldID" name="content" v-if="configurable" type="text" class="materialize-textarea"></textarea>
            <textarea :id="fieldID" name="content" v-else v-model="text" type="text" class="materialize-textarea"></textarea>
        </div>
        <div class="col s12" v-if="configurable">
            <ul class="collapsible">
                <li>
                  <div class="collapsible-header"><i class="material-icons">settings</i>Config Text Format</div>
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
