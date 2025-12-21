	<div v-show="sectionActive == 'home'">
		<div class="row">
			<!-- Configuration Summary Cards with Gradients -->
			<div class="col s12 m6 l3">
				<div v-on:click="changeSectionActive('general')" class="config-card card-panel gradient-blue white-text">
					<i class="material-icons card-icon">build</i>
					<h5 class="card-count">@{{generalConfigurations.length}}</h5>
					<p class="card-label">{{ lang('config_general') }}</p>
				</div>
			</div>
			<div class="col s12 m6 l3">
				<div v-on:click="changeSectionActive('theme')" class="config-card card-panel gradient-pink white-text">
					<i class="material-icons card-icon">brush</i>
					<h5 class="card-count">@{{themeConfigurations.length}}</h5>
					<p class="card-label">{{ lang('config_theme') }}</p>
				</div>
			</div>
			<div class="col s12 m6 l3">
				<div v-on:click="changeSectionActive('seo')" class="config-card card-panel gradient-green white-text">
					<i class="material-icons card-icon">trending_up</i>
					<h5 class="card-count">@{{seoConfigurations.length}}</h5>
					<p class="card-label">{{ lang('config_seo') }}</p>
				</div>
			</div>
			<div class="col s12 m6 l3">
				<div v-on:click="changeSectionActive('database')" class="config-card card-panel gradient-amber white-text">
					<i class="material-icons card-icon">sd_card</i>
					<h5 class="card-count">@{{files.length}}</h5>
					<p class="card-label">{{ lang('config_database') }}</p>
				</div>
			</div>
		</div>

		<div class="row">
			<!-- Health Checks & Alerts (New) -->
			<div class="col s12 m12 l4">
				<div class="card dashboard-widget">
					<div class="card-content">
						<span class="card-title widget-title">
							<i class="material-icons text-red">security</i> Health Check
						</span>
						<div class="health-check-list" v-if="healthIssues.length > 0">
							<div v-for="issue in healthIssues" :class="'alert-item alert-' + issue.type">
								<i v-if="issue.type == 'warning'" class="material-icons tiny">warning</i>
								<i v-else-if="issue.type == 'info'" class="material-icons tiny">info</i>
								<i v-else-if="issue.type == 'success'" class="material-icons tiny">check_circle</i>
								<div class="alert-content">
									<b class="alert-title">@{{issue.title}}</b>
									<span class="alert-msg">@{{issue.message}}</span>
								</div>
							</div>
						</div>
						<div v-else class="center-align" style="padding: 20px;">
							<i class="material-icons medium text-green">check_circle</i>
							<p>Your system is healthy!</p>
						</div>
					</div>
				</div>

				<!-- Quick Toggles -->
				<div class="card dashboard-widget">
					<div class="card-content">
						<span class="card-title widget-title">Quick Settings</span>
						<ul class="quick-settings-list">
							<li>
								<span>Analytics Tracking</span>
								<div class="switch tiny">
									<label><input type="checkbox" :checked="getConfigValueBoolean('ANALYTICS_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'ANALYTICS_ACTIVE')"><span class="lever"></span></label>
								</div>
							</li>
							<li>
								<span>Facebook Pixel</span>
								<div class="switch tiny">
									<label><input type="checkbox" :checked="getConfigValueBoolean('PIXEL_ACTIVE')" v-on:change="updateConfigCheckbox($event, 'PIXEL_ACTIVE')"><span class="lever"></span></label>
								</div>
							</li>
							<li>
								<span>System Logger</span>
								<div class="switch tiny">
									<label><input type="checkbox" :checked="getConfigValueBoolean('SYSTEM_LOGGER')" v-on:change="updateConfigCheckbox($event, 'SYSTEM_LOGGER')"><span class="lever"></span></label>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Main Info Center -->
			<div class="col s12 m12 l8">
				<div class="card dashboard-widget">
					<div class="card-content">
						<div class="row" style="margin-bottom: 0;">
							<div class="col s12 m7">
								<span class="card-title widget-title">
									<i class="material-icons">storage</i> System Performance
								</span>
								<div v-if="systemInfo" class="system-stats">
									<div class="stat-item">
										<div class="stat-info">
											<span>Disk Usage</span>
											<span>@{{systemInfo.disk_usage_pct}}% (@{{systemInfo.disk_free}} free)</span>
										</div>
										<div class="progress-container">
											<div class="progress-bar" :style="{width: systemInfo.disk_usage_pct + '%', backgroundColor: systemInfo.disk_usage_pct > 80 ? '#f44336' : '#2196f3'}"></div>
										</div>
									</div>
									<div class="row system-details" style="margin-top:20px;">
										<div class="col s4 center-align">
											<div class="detail-val">@{{systemInfo.php_version}}</div>
											<div class="detail-label">PHP</div>
										</div>
										<div class="col s4 center-align">
											<div class="detail-val">@{{systemInfo.db_driver}}</div>
											<div class="detail-label">Database</div>
										</div>
										<div class="col s4 center-align">
											<div class="detail-val">@{{systemInfo.max_upload}}</div>
											<div class="detail-label">Max Upload</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col s12 m5 center-align">
								<div style="height: 180px; position: relative; margin-top: 10px;">
									<canvas id="configDistributionChart"></canvas>
								</div>
								<p class="chart-caption">Config Distribution</p>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<!-- Activity Log -->
					<div class="col s12 m6">
						<div class="card dashboard-widget">
							<div class="card-content">
								<span class="card-title widget-title">Recent Activity</span>
								<ul class="activity-feed">
									<li v-for="item in recentActivity">
										<div class="activity-dot"></div>
										<div class="activity-info">
											<b class="activity-name">@{{item.config_label || item.config_name}}</b>
											<span class="activity-time">@{{item.date_update}}</span>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- Recent Backups -->
					<div class="col s12 m6">
						<div class="card dashboard-widget">
							<div class="card-content">
								<span class="card-title widget-title">Recent Backups</span>
								<div v-if="files.length > 0">
									<ul class="backup-mini-list">
										<li v-for="file in files.slice(0, 3)">
											<i class="material-icons tiny">description</i>
											<span class="backup-name">@{{file.get_filename()}}</span>
											<span class="backup-date">@{{file.date_create.split(' ')[0]}}</span>
										</li>
									</ul>
									<a @click="changeSectionActive('database')" class="btn-flat waves-effect waves-teal full-width center-align" style="margin-top: 10px;">View All Backups</a>
								</div>
								<div v-else class="center-align" style="padding: 10px;">
									<p>No backups found.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
