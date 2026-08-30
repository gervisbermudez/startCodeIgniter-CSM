<div v-show="sectionActive == 'system'" class="col s12">
    <div class="config-section-header">
        <h2 class="page-header">
            <i class="material-icons left" aria-hidden="true">build</i> <?php echo lang('config_system_maintenance'); ?>
        </h2>
        <p class="section-description"><?php echo lang('config_system_maintenance_desc'); ?></p>
    </div>

    <div v-if="systemConfigurations.length > 0">
        <configuration v-for="configuration in systemConfigurations" :key="'sys-' + configuration.site_config_id" :configuration="configuration"></configuration>
    </div>

    <div v-if="loggerConfig.length > 0">
        <h2 class="page-header"><?php echo lang('config_logger_settings'); ?></h2>
        <configuration v-for="configuration in loggerConfig" :key="'log-' + configuration.site_config_id" :configuration="configuration"></configuration>
    </div>

    <div class="center" v-if="systemConfigurations.length == 0 && loggerConfig.length == 0">
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-green-only">
              <div class="circle-clipper left"><div class="circle"></div></div>
              <div class="gap-patch"><div class="circle"></div></div>
              <div class="circle-clipper right"><div class="circle"></div></div>
            </div>
        </div>
        <p class="grey-text"><?php echo lang('loading_system_configs'); ?></p>
    </div>

    <div class="card-panel" v-if="lastCleanupResult">
        <span>
            <i class="material-icons left teal-text" aria-hidden="true">check_circle</i>
            <?php echo lang('last_maintenance'); ?>:
            <?php echo str_replace('@count', '@{{lastCleanupResult.system_logs}}', lang('system_logs_cleaned')); ?>,
            <?php echo str_replace('@count', '@{{lastCleanupResult.api_logs}}', lang('error_logs_cleaned')); ?> <?php echo lang('and'); ?>
            <?php echo str_replace('@count', '@{{lastCleanupResult.user_tracking}}', lang('user_tracking_cleaned')); ?>.
        </span>
    </div>
</div>
