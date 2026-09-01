<div v-show="sectionActive == 'import'">
    <div class="config-section-header">
        <h2 class="page-header">{{ lang('config_import') }}</h2>
        <p class="section-description">{{ lang('config_choose_file') }}</p>
    </div>
    <div class="center" v-show="loader">
        <preloader />
    </div>
    <div class="card z-depth-1" v-show="!loader">
        <div class="card-content">
            <div class="file-field input-field">
                <div class="btn waves-effect waves-light btn-accent">
                    <span>{{ lang('config_select_file') }}</span>
                    <input type="file" id="files" name="files[]" v-on:change="handleFileSelect" />
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
            <ul class="collapsible config-pick" data-collapsible="expandable" v-show="selectedFile">
                <li v-for="group in pickerGroups" :key="'im-' + group.key" v-if="showImportGroup(group)">
                    <div class="collapsible-header config-pick-header">
                        <label class="config-pick-check" @click.stop aria-label="{{ lang('config_select_all') }}">
                            <input type="checkbox" class="filled-in"
                                :checked="groupAllChecked('importData', group)"
                                :indeterminate.prop="groupSomeChecked('importData', group)"
                                v-on:change="onGroupSelectAll('importData', group, $event)" />
                            <span></span>
                        </label>
                        <i class="material-icons" aria-hidden="true">@{{ group.icon }}</i>
                        <span class="config-pick-header__label">@{{ groupLabel(group) }}</span>
                        <span class="config-pick-header__count">@{{ groupSelectedCount('importData', group.key) }}/@{{ groupItems('importData', group.key).length }}</span>
                    </div>
                    <div class="collapsible-body config-pick-body">
                        <div class="config-pick-search" v-if="groupItems('importData', group.key).length">
                            <input type="search" placeholder="{{ lang('config_export_search') }}" v-model="pickerSearch[group.key]" />
                        </div>
                        <p class="config-pick-empty" v-if="!visibleGroupItems('importData', group).length">{{ lang('list_filter_empty') }}</p>
                        <ul class="config-pick-list" v-else>
                            <li class="config-pick-row" v-for="(item, index) in visibleGroupItems('importData', group)" :key="'im-' + group.key + '-' + index">
                                <label class="config-pick-check">
                                    <input type="checkbox" class="filled-in" v-model="item.checked" />
                                    <span></span>
                                </label>
                                <div class="config-pick-row__text">
                                    <span class="config-pick-row__title">@{{ item[group.titleField] || item.name || item.config_label || item.title || '—' }}</span>
                                    <span class="config-pick-row__meta" v-if="group.metaField && item[group.metaField] && item[group.metaField] !== item[group.titleField]">@{{ item[group.metaField] }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="config-actions" v-show="selectedFile">
                <button type="button" class="btn waves-effect waves-light btn-accent" @click="saveData()" :disabled="!btnEnable">
                    <i class="material-icons left">save</i> {{ lang('config_import') }}
                </button>
            </div>
        </div>
    </div>
</div>
