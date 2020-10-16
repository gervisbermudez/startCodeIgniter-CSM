<script type="text/x-template" id="formImageSelector-template">
  <div class="row formImageSelector">
      <div class="col s12">
          <b>Field Preview:</b>
      </div>
      <div class="input-field col s12">
          <a type="button" class="btn btn-primary modal-trigger" href="#folderSelector">
              <span><i class="material-icons right">image</i> @{{fieldName ? fieldName : 'Agregar imagen'}}</span>
          </a>
      </div>
      <div class="row" v-if="preselected.length">
        <div class="col s12 m4" v-for="(album_item, index) in preselected" :key="index">
            <div class="card">
                <div class="card-image">
                    <div class="card-image-container">
                        <img class="materialboxed" :src="getPageImagePath(album_item)" />
                    </div>
                    <a @click="removeItemImage(index);" class="btn-floating halfway-fab waves-effect waves-light" href='#!'>
                        <i class="material-icons">delete</i></a>
                </div>
            </div>
        </div>
    </div>
      <div class="col s12" v-if="configurable">
          <ul class="collapsible">
              <li>
                  <div class="collapsible-header">
                      <i class="material-icons">settings</i>Config Selector
                  </div>
                  <div class="collapsible-body">
                      <div class="row">
                          <div class="input-field col s12">
                              Field Name
                              <br />
                              <input placeholder="Field Name" @keyup="convertfielApiID()" v-model="fieldName" type="text"
                                  class="validate" />
                          </div>
                          <div class="input-field col s12">
                              Api ID
                              <input placeholder="Api ID" v-model="fielApiID" type="text" class="validate" />
                          </div>
                          <div class="input-field col s12">
                              Multiple
                          <div class="switch">
                              <label>
                              Off
                              <input type="checkbox" v-model="multiple">
                              <span class="lever"></span>
                              On
                              </label>
                          </div>
                          </div>

                      </div>
                  </div>
              </li>
          </ul>
      </div>
      <file-explorer-selector :preselected="preselected" :modal="'folderSelector'" :mode="mode" :filter="filter"
          :multiple="multiple" v-on:notify="copyCallcack"></file-explorer-selector>
  </div>
</script>
