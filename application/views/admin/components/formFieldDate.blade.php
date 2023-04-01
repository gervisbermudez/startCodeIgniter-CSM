<script type="text/x-template" id="formFieldDate-template">
    <div class="row formFieldDate">
        <div class="col s12" v-if="configurable">
          <b>Field Preview:</b>
      </div>
        <div class="input-field col s12">
            <input :placeholder="fieldPlaceholder" v-if="configurable" v-model="date" :id="fieldID" type="text" class="datepicker validate">
            <input :placeholder="fieldPlaceholder" v-else @change="setDate()" :id="fieldID" type="text" class="datepicker validate">
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
                        <div class="input-field col s12">
                            Format
                            <input placeholder="Format" v-model="fielFormat" type="text" class="validate">
                        </div>
                    </div>
                  </div>
                </li>
              </ul>
        </div>
    </div>
</script>
