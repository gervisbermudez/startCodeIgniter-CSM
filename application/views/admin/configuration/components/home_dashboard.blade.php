	<div v-show="sectionActive == 'home'">
		<div class="row">
			<div class="col s12 m12 l4">
				<div class="card z-depth-1 dashboard-widget">
					<div class="card-content">
						<span class="widget-title">
							<i class="material-icons" aria-hidden="true">verified_user</i> {{ lang('config_health_check') }}
						</span>
						<div class="health-check-list" v-if="healthIssues.length > 0">
							<div v-for="issue in healthIssues" :key="issue.title" :class="'alert-item alert-' + issue.type">
								<i v-if="issue.type == 'warning'" class="material-icons tiny" aria-hidden="true">warning</i>
								<i v-else-if="issue.type == 'info'" class="material-icons tiny" aria-hidden="true">info</i>
								<i v-else-if="issue.type == 'success'" class="material-icons tiny" aria-hidden="true">check_circle</i>
								<div class="alert-content">
									<b class="alert-title">@{{issue.title}}</b>
									<span class="alert-msg">@{{issue.message}}</span>
								</div>
							</div>
						</div>
						<div v-else class="center-align health-empty">
							<i class="material-icons medium" aria-hidden="true">check_circle</i>
							<p>{{ lang('config_system_healthy') }}</p>
						</div>
					</div>
				</div>

				<div class="card z-depth-1 dashboard-widget">
					<div class="card-content">
						<span class="widget-title">{{ lang('config_quick_settings') }}</span>
						<ul class="quick-settings-list">
							<li>
								<span>{{ lang('config_analytics_tracking') }}</span>
								<div class="switch">
									<label>
										<input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')">
										<span class="lever"></span>
									</label>
								</div>
							</li>
							<li>
								<span>{{ lang('config_facebook_pixel') }}</span>
								<div class="switch">
									<label>
										<input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')">
										<span class="lever"></span>
									</label>
								</div>
							</li>
							<li>
								<span>{{ lang('config_system_logger') }}</span>
								<div class="switch">
									<label>
										<input type="checkbox" :checked="getConfigValueBoolean('SYSTEM_LOGGER')" v-on:change="updateConfigCheckbox($event, 'SYSTEM_LOGGER')">
										<span class="lever"></span>
									</label>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="col s12 m12 l8">
				<div class="card z-depth-1 dashboard-widget">
					<div class="card-content">
						<span class="widget-title">
							<i class="material-icons" aria-hidden="true">storage</i> {{ lang('config_system_performance') }}
						</span>
						<div v-if="systemInfo" class="system-stats">
							<div class="stat-item">
								<div class="stat-info">
									<span>{{ lang('config_disk_usage') }}</span>
									<span>@{{systemInfo.disk_usage_pct}}% (@{{systemInfo.disk_free}} {{ lang('config_disk_free') }})</span>
								</div>
								<div class="progress">
									<div class="determinate" :class="{'red': systemInfo.disk_usage_pct > 80, 'teal': systemInfo.disk_usage_pct <= 80}" :style="{width: systemInfo.disk_usage_pct + '%'}"></div>
								</div>
							</div>
							<div class="row system-details">
								<div class="col s4 center-align">
									<div class="detail-val">@{{systemInfo.php_version}}</div>
									<div class="detail-label">PHP</div>
								</div>
								<div class="col s4 center-align">
									<div class="detail-val">@{{systemInfo.db_driver}}</div>
									<div class="detail-label">{{ lang('config_database') }}</div>
								</div>
								<div class="col s4 center-align">
									<div class="detail-val">@{{systemInfo.max_upload}}</div>
									<div class="detail-label">{{ lang('config_max_upload') }}</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col s12 m6">
						<div class="card z-depth-1 dashboard-widget">
							<div class="card-content">
								<span class="widget-title">{{ lang('config_recent_activity') }}</span>
								<ul class="activity-feed" v-if="recentActivity.length">
									<li v-for="item in recentActivity" :key="item.site_config_id">
										<div class="activity-dot"></div>
										<div class="activity-info">
											<b class="activity-name">@{{item.config_label || item.config_name}}</b>
											<span class="activity-time">@{{item.date_update}}</span>
										</div>
									</li>
								</ul>
								<p v-else class="grey-text">{{ lang('config_no_activity') }}</p>
							</div>
						</div>
					</div>
					<div class="col s12 m6">
						<div class="card z-depth-1 dashboard-widget">
							<div class="card-content">
								<span class="widget-title">{{ lang('config_recent_backups') }}</span>
								<div v-if="recentBackupPreview.length > 0">
									<ul class="backup-mini-list">
										<li v-for="file in recentBackupPreview" :key="file.file_id">
											<i class="material-icons tiny" aria-hidden="true">description</i>
											<span class="backup-name">@{{file.get_filename()}}</span>
											<span class="backup-date">@{{file.date_create.split(' ')[0]}}</span>
										</li>
									</ul>
									<a :href="base_url('admin/configuration/data')" class="btn-flat waves-effect waves-teal full-width center-align">{{ lang('config_view_all_backups') }}</a>
								</div>
								<div v-else class="center-align">
									<p class="grey-text">{{ lang('config_no_backups') }}</p>
									<a :href="base_url('admin/configuration/data')" class="btn-flat waves-effect waves-teal">{{ lang('config_view_all_backups') }}</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
