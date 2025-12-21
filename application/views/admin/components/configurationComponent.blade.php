<script type="text/x-template" id="configurationComponent-template">
    <ul class="collapsible expandable configuration-component">
		<li  class="">
			<div class="collapsible-header"><i class="material-icons">filter_drama</i>@{{(configuration.config_label || configuration.config_name) | capitalize}}
				<div class="switch" v-if="handle_value_as == 'switch' && configuration.config_data.perm_values">
					<label>
						@{{configuration.config_data.perm_values[0]}}
						<input type="checkbox" :checked="isChecked" v-on:change="switchCahnged($event);">
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
				<div v-if="handle_value_as == 'select' && configuration.config_data.perm_values">
					<select v-model="configuration.config_value" v-on:change="saveConfig();">
						<option value="" disabled selected>Choose your option</option>
						<option v-for="(value, i) in configuration.config_data.perm_values" v-if="(typeof value == 'string')" :value="value">@{{value}}</option>
					</select>
				</div>
				<div class="input-field" v-if="handle_value_as == 'input'">
					<input :id="'last_name' + makeid(10)" :type="configuration.config_data.input_type || 'text'" class="validate" :class="{ invalid: configuration.validate === false, valid: configuration.validate === true }" v-model="configuration.config_value" v-on:blur="saveConfig();" v-on:focus="focusInput();">
				</div>
				<div class="row" style="margin-bottom: 0; margin-top: 10px; border-top: 1px solid #f4f4f4; padding-top: 10px;">
					<div class="col s12">
						<span class="grey-text" style="font-size: 0.8rem;">Key: @{{configuration.config_name}}</span>
					</div>
				</div>
			</div>
		</li>
	</ul>
</script>
