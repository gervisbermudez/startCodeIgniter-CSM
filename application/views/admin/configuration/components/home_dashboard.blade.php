	<div v-show="sectionActive == 'home'" class="config-overview">
		<div class="config-section-header">
			<h2 class="page-header">{{ lang('dashboard_overview') }}</h2>
			<p class="section-description">{{ lang('config_overview_lede') }}</p>
		</div>

		<div class="config-overview-hero">
			<div class="card z-depth-1 dashboard-widget config-overview-value">
				<div class="card-content">
					<span class="widget-title">
						<i class="material-icons" aria-hidden="true">storefront</i> {{ lang('config_value_title') }}
					</span>
					<h3 class="config-overview-value__name">@{{ overviewView.site.title || '—' }}</h3>
					<p class="config-overview-value__desc" v-if="overviewView.site.description">@{{ overviewView.site.description }}</p>
					<p class="config-overview-value__desc" v-else>{{ lang('config_value_empty_desc') }}</p>
					<p class="config-overview-value__pitch">@{{ sitePitch }}</p>
					<div class="config-overview-value__actions">
						<a :href="overviewView.site.public_url || base_url('')" class="btn btn-accent waves-effect waves-light" target="_blank" rel="noopener">{{ lang('config_overview_view_site') }}</a>
						<button type="button" class="btn-flat waves-effect waves-teal" v-on:click="changeSectionActive('general')">{{ lang('config_overview_edit_site') }}</button>
					</div>
				</div>
			</div>

			<div class="card z-depth-1 dashboard-widget config-overview-health">
				<div class="card-content">
					<div class="config-overview-health__head">
						<span class="widget-title">
							<i class="material-icons" aria-hidden="true">verified_user</i> {{ lang('config_health_check') }}
						</span>
						<span class="config-health-badge" :class="'is-' + healthStatus">@{{ healthStatusLabel }}</span>
					</div>
					<div class="config-overview-health__score">
						<strong>@{{ overviewView.health.score }}</strong>
						<span>{{ lang('config_health_score') }}</span>
					</div>
					<div class="health-check-list" v-if="healthIssues.length > 0">
						<div
							v-for="issue in healthIssues"
							:key="issue.id || issue.title"
							:class="'alert-item alert-' + issue.type + (issue.href ? ' is-link' : '')"
							:role="issue.href ? 'link' : null"
							:tabindex="issue.href ? 0 : -1"
							@click="openHealthIssue(issue)"
							@keyup.enter="openHealthIssue(issue)"
						>
							<i v-if="issue.type == 'critical' || issue.type == 'warning'" class="material-icons tiny" aria-hidden="true">warning</i>
							<i v-else-if="issue.type == 'info'" class="material-icons tiny" aria-hidden="true">info</i>
							<i v-else class="material-icons tiny" aria-hidden="true">check_circle</i>
							<div class="alert-content">
								<b class="alert-title">@{{ issue.title }}</b>
								<span class="alert-msg">@{{ issue.message }}</span>
							</div>
						</div>
					</div>
					<div v-else class="center-align health-empty">
						<i class="material-icons medium" aria-hidden="true">check_circle</i>
						<p>{{ lang('config_system_healthy') }}</p>
						<p class="config-help">{{ lang('config_health_ok_desc') }}</p>
					</div>
				</div>
			</div>
		</div>

		<div class="config-overview-kpis" role="group" aria-label="{{ lang('dashboard_overview') }}">
			<a class="config-overview-kpi" :href="base_url('admin/pages')">
				<span class="config-overview-kpi__label">{{ lang('config_kpi_pages') }}</span>
				<strong>@{{ formatCount(overviewView.content.pages) }}</strong>
				<span class="config-overview-kpi__hint">@{{ formatCount(overviewView.content.drafts) }} {{ lang('config_content_drafts') }}</span>
			</a>
			<a class="config-overview-kpi" :href="base_url('admin/configuration/logs?tab=tracking')">
				<span class="config-overview-kpi__label">{{ lang('config_kpi_visits') }}</span>
				<strong>@{{ formatCount(overviewView.activity.visits_7) }}</strong>
				<span class="config-overview-kpi__hint">@{{ formatCount(overviewView.activity.unique_visitors_7) }} {{ lang('config_kpi_visitors') }}</span>
			</a>
			<a class="config-overview-kpi" :href="base_url('admin/configuration/logs?tab=system')">
				<span class="config-overview-kpi__label">{{ lang('config_kpi_editors') }}</span>
				<strong>@{{ formatCount(overviewView.activity.cms_7) }}</strong>
				<span class="config-overview-kpi__hint">{{ lang('config_logs_system') }}</span>
			</a>
			<a class="config-overview-kpi" :href="base_url('admin/siteforms')">
				<span class="config-overview-kpi__label">{{ lang('config_kpi_messages') }}</span>
				<strong>@{{ formatCount(overviewView.activity.messages_7) }}</strong>
				<span class="config-overview-kpi__hint">@{{ formatCount(overviewView.content.forms) }} {{ lang('config_content_forms') }}</span>
			</a>
		</div>

		<div class="config-overview-grid">
			<div class="card z-depth-1 dashboard-widget">
				<div class="card-content">
					<div class="config-overview-health__head">
						<span class="widget-title">
							<i class="material-icons" aria-hidden="true">show_chart</i> {{ lang('config_activity_chart') }}
						</span>
						<a :href="base_url('admin/configuration/logs')" class="btn-flat btn-small waves-effect waves-teal">{{ lang('menu_logs') }}</a>
					</div>
					<p class="config-help">{{ lang('config_activity_chart_help') }}</p>
					<div class="config-chart-wrap config-chart-wrap--overview" v-show="hasOverviewTrend">
						<canvas id="overviewTrendChart" aria-label="{{ lang('config_activity_chart') }}"></canvas>
					</div>
					<p class="config-help" v-if="!overviewLoading && !hasOverviewTrend">{{ lang('config_logs_no_data') }}</p>
					<div class="config-overview-chart-legend" v-if="hasOverviewTrend">
						<span><i class="config-chart-legend__swatch is-visits" aria-hidden="true"></i> {{ lang('config_activity_visits') }}</span>
						<span><i class="config-chart-legend__swatch is-cms" aria-hidden="true"></i> {{ lang('config_activity_cms') }}</span>
					</div>
				</div>
			</div>

			<div class="card z-depth-1 dashboard-widget">
				<div class="card-content">
					<span class="widget-title">
						<i class="material-icons" aria-hidden="true">inventory_2</i> {{ lang('config_content_title') }}
					</span>
					<ul class="config-overview-inventory">
						<li>
							<a :href="base_url('admin/pages')">{{ lang('config_content_pages') }}</a>
							<strong>@{{ formatCount(overviewView.content.pages) }}</strong>
						</li>
						<li>
							<a :href="base_url('admin/pages')">{{ lang('config_content_drafts') }}</a>
							<strong>@{{ formatCount(overviewView.content.drafts) }}</strong>
						</li>
						<li>
							<a :href="base_url('admin/files')">{{ lang('config_content_files') }}</a>
							<strong>@{{ formatCount(overviewView.content.files) }}</strong>
						</li>
						<li>
							<a :href="base_url('admin/siteforms')">{{ lang('config_content_forms') }}</a>
							<strong>@{{ formatCount(overviewView.content.forms) }}</strong>
						</li>
						<li>
							<a :href="base_url('admin/users')">{{ lang('config_content_users') }}</a>
							<strong>@{{ formatCount(overviewView.content.users) }}</strong>
						</li>
						<li>
							<a :href="base_url('admin/custommodels')">{{ lang('config_content_collections') }}</a>
							<strong>@{{ formatCount(overviewView.content.collections) }}</strong>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="config-overview-lower">
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

			<div class="card z-depth-1 dashboard-widget">
				<div class="card-content">
					<span class="widget-title">{{ lang('config_recent_backups') }}</span>
					<div v-if="recentBackupPreview.length > 0">
						<ul class="backup-mini-list">
							<li v-for="file in recentBackupPreview" :key="file.filename">
								<i class="material-icons tiny" aria-hidden="true">description</i>
								<span class="backup-name">@{{ file.filename }}</span>
								<span class="backup-date">@{{ (file.date_create || '').split(' ')[0] }}</span>
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
	</div>
