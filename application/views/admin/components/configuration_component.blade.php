<script type="text/x-template" id="configurationComponent-template">
    <ul class="collapsible expandable configuration-component">
		<li>
			<div class="collapsible-header">
                <i class="material-icons" aria-hidden="true">settings</i>
                @{{(configuration.config_label || configuration.config_name) | capitalize}}
				<div class="switch" v-if="handle_value_as == 'switch' && configuration.config_data.perm_values">
					<label>
						@{{switchOffLabel}}
						<input type="checkbox" :checked="isChecked" v-on:change="switchCahnged($event);">
						<span class="lever"></span>
						@{{switchOnLabel}}
					</label>
				</div>
				<span class="current" v-show="show_label">@{{configuration.config_value}}</span>
				<i class="material-icons arrow right" v-show="show_arrow" aria-hidden="true">keyboard_arrow_right</i>
			</div>
			<div class="collapsible-body" :class="{ 'collapsible-body-hidden': !show_body}" v-show="show_body">
                <p class="config-help" v-if="configuration.config_description">@{{configuration.config_description}}</p>
				<span class="current"><b>@{{configuration.config_value}}</b></span>
				<div v-if="handle_value_as == 'select' && configuration.config_data.perm_values">
					<select v-model="configuration.config_value" v-on:change="saveConfig();">
						<option value="" disabled><?php echo lang('config_choose_option'); ?></option>
						<option v-for="(value, key) in configuration.config_data.perm_values" :key="key" :value="selectOptionValue(key, value)">@{{selectOptionLabel(value)}}</option>
					</select>
				</div>
				<div class="input-field" v-if="handle_value_as == 'input'">
					<input :id="'config-field-' + configuration.site_config_id" :type="configuration.config_data.input_type || 'text'" class="validate" :class="{ invalid: configuration.validate === false, valid: configuration.validate === true }" v-model="configuration.config_value" v-on:blur="saveConfig();" v-on:focus="focusInput();" :readonly="configuration.readonly == 1">
				</div>
				<div class="config-key">
                    <?php echo lang('config_key'); ?>: @{{configuration.config_name}}
				</div>
			</div>
		</li>
	</ul>
</script>
