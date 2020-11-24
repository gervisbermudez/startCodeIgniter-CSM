<script type="text/x-template" id="dataEditComponent-template">
<div id="dataEditComponent-root">
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    <div class="configurations" v-cloak v-if="!loader">
        <div class="row">
            <div class="col s12">
                <div class="row" v-for="(key, index) in keys" :key="index">
                    <div class="input-field col s12">
                        <input :placeholder="key" :id="key + index" type="text" class="validate" v-model="data.item[key]">
                        <label :for="key + '_' + index" class="active">@{{key}}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <a v-on:click="save" class="waves-effect waves-light btn"><i class="material-icons left">cloud</i>button</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>