<div v-show="sectionActive == 'export'">
    <div class="config-section-header">
        <h2 class="page-header">{{ lang('config_export') }}</h2>
        <p class="section-description">{{ lang('config_export_desc') }}</p>
    </div>
    <div class="center" v-show="loader">
        <preloader />
    </div>
    <div class="card z-depth-1" v-show="!loader">
        <div class="card-content">
            <label class="config-pick-unpublished">
                <input type="checkbox" class="filled-in" v-model="includeUnpublishedPages" @change="getData()" />
                <span>{{ lang('config_export_include_unpublished') }}</span>
            </label>
            <ul class="collapsible config-pick" data-collapsible="expandable">
                <li v-for="group in pickerGroups" :key="'ex-' + group.key">
                    <div class="collapsible-header config-pick-header">
                        <label class="config-pick-check" @click.stop aria-label="{{ lang('config_select_all') }}">
                            <input type="checkbox" class="filled-in"
                                :checked="groupAllChecked('catalogData', group)"
                                :indeterminate.prop="groupSomeChecked('catalogData', group)"
                                :disabled="!groupItems('catalogData', group.key).length"
                                v-on:change="onGroupSelectAll('catalogData', group, $event)" />
                            <span></span>
                        </label>
                        <i class="material-icons" aria-hidden="true">@{{ group.icon }}</i>
                        <span class="config-pick-header__label">@{{ groupLabel(group) }}</span>
                        <span class="config-pick-header__count">@{{ groupSelectedCount('catalogData', group.key) }}/@{{ groupItems('catalogData', group.key).length }}</span>
                    </div>
                    <div class="collapsible-body config-pick-body">
                        <div class="config-pick-search" v-if="groupItems('catalogData', group.key).length">
                            <input type="search" placeholder="{{ lang('config_export_search') }}" v-model="pickerSearch[group.key]" />
                        </div>
                        <p class="config-pick-empty" v-if="!groupItems('catalogData', group.key).length">{{ lang('config_export_group_empty') }}</p>
                        <p class="config-pick-empty" v-else-if="!visibleGroupItems('catalogData', group).length">{{ lang('list_filter_empty') }}</p>
                        <ul class="config-pick-list" v-else>
                            <li class="config-pick-row" v-for="(item, index) in visibleGroupItems('catalogData', group)" :key="'ex-' + group.key + '-' + index">
                                <label class="config-pick-check">
                                    <input type="checkbox" class="filled-in" v-model="item.checked" />
                                    <span></span>
                                </label>
                                <div class="config-pick-row__text">
                                    <span class="config-pick-row__title">@{{ item[group.titleField] || item.name || '—' }}</span>
                                    <span class="config-pick-row__meta" v-if="group.metaField && item[group.metaField] && item[group.metaField] !== item[group.titleField]">@{{ item[group.metaField] }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="config-actions">
                <button type="button" class="btn-flat waves-effect" @click="exportAllItems()" :disabled="!catalogHasItems">
                    <i class="material-icons left">select_all</i> {{ lang('config_export_all') }}
                </button>
                <button type="button" class="btn waves-effect waves-light btn-accent" @click="generateFile()" :disabled="!btnEnable">
                    <i class="material-icons left">file_download</i> {{ lang('config_export') }}
                </button>
            </div>
        </div>
    </div>
</div>
