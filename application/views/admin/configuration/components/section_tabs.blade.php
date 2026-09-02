        <div class="config-section-tabs" v-show="isSiteSection" role="tablist" aria-label="<?php echo lang('config_site'); ?>">
            <button type="button" class="status-chip" :class="{active: sectionActive == 'general'}" v-on:click="changeSectionActive('general')" role="tab" :aria-selected="sectionActive == 'general' ? 'true' : 'false'"><?php echo lang('config_general'); ?></button>
            <button type="button" class="status-chip" :class="{active: sectionActive == 'theme'}" v-on:click="changeSectionActive('theme')" role="tab" :aria-selected="sectionActive == 'theme' ? 'true' : 'false'"><?php echo lang('config_appearance'); ?></button>
            <button type="button" class="status-chip" :class="{active: sectionActive == 'seo'}" v-on:click="changeSectionActive('seo')" role="tab" :aria-selected="sectionActive == 'seo' ? 'true' : 'false'"><?php echo lang('config_seo'); ?></button>
        </div>
        <div class="config-section-tabs" v-show="isSystemSection" role="tablist" aria-label="<?php echo lang('config_system'); ?>">
            <button type="button" class="status-chip" :class="{active: sectionActive == 'system'}" v-on:click="changeSectionActive('system')" role="tab" :aria-selected="sectionActive == 'system' ? 'true' : 'false'"><?php echo lang('config_system_maintenance'); ?></button>
            <button type="button" class="status-chip" :class="{active: sectionActive == 'updater'}" v-on:click="changeSectionActive('updater')" role="tab" :aria-selected="sectionActive == 'updater' ? 'true' : 'false'"><?php echo lang('config_updates'); ?></button>
        </div>
