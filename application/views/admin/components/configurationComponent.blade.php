<script type="text/x-template" id="configurationComponent-template">
    <ul class="collapsible expandable configuration-component">
		<li  class="">
			<div class="collapsible-header"><i class="material-icons">filter_drama</i>@{{configuration.config_name | capitalize}}
				<div class="switch" v-if="handle_value_as == 'switch'">
					<label>
						@{{configuration.config_data.perm_values[0]}}
						<input type="checkbox" v-model="configuration.config_value" v-on:change="saveConfig();">
						<span class="lever"></span>
						@{{configuration.config_data.perm_values[1]}}
					</label>
				</div>
				<span class="current" v-show="show_label">@{{configuration.config_value}}</span>
				<i class="material-icons arrow right" v-show="show_arrow">keyboard_arrow_right</i>
			</div>
			<div class="collapsible-body" :class="{ 'collapsible-body-hidden': !show_body}" v-show="show_body">
				<span class="current"><b>@{{configuration.config_value}}</b></span>
				<br />
				<div v-if="handle_value_as == 'select' && (typeof configuration.config_data.perm_values == 'object')">
					<select v-model="configuration.config_value" v-on:change="saveConfig();">
						<option value="" disabled selected>Choose your option</option>
						<option v-for="(value, i) in configuration.config_data.perm_values" v-if="(typeof value == 'string')" :value="value">@{{value}}</option>
					</select>
				</div>
				<div class="input-field" v-if="configuration.config_data.perm_values == null">
					<input :id="'last_name' + makeid(10)" :type="configuration.config_data.input_type" class="validate" :class="{ invalid: configuration.validate === false, valid: configuration.validate === true }" v-model="configuration.config_value" v-on:blur="saveConfig();">
				</div>
			</div>
		</li>
	</ul>
</script>
