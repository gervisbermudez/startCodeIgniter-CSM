@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/userprofile.min.css')?>">
@endsection

@section('content')
<div id="root" class="user-profile-root">
    <div class="row">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <preloader />
        </div>
    </div>
    <div class="row user-profile" v-cloak v-show="!loader">
        <div class="col s12">
            <div class="user-profile-kpis">
                <template v-for="card in kpiCards">
                    <a
                        v-if="card.can"
                        :key="'kpi-a-' + card.key"
                        class="kpi-card"
                        :class="'kpi-card--' + card.key"
                        :href="base_url(card.href)"
                    >
                        <i class="material-icons kpi-icon" aria-hidden="true">@{{ card.icon }}</i>
                        <div class="kpi-value">@{{ countOf(card.key) }}</div>
                        <div class="kpi-label">@{{ lang(card.labelKey) }}</div>
                    </a>
                    <div
                        v-else
                        :key="'kpi-d-' + card.key"
                        class="kpi-card"
                        :class="'kpi-card--' + card.key"
                    >
                        <i class="material-icons kpi-icon" aria-hidden="true">@{{ card.icon }}</i>
                        <div class="kpi-value">@{{ countOf(card.key) }}</div>
                        <div class="kpi-label">@{{ lang(card.labelKey) }}</div>
                    </div>
                </template>
            </div>
        </div>
        <div class="col s12 m5 l4 user-profile-aside">
            <div class="card banner">
                <a class="dropdown-trigger right tooltipped" href="#!" data-target="user-profile-actions" data-position="left" data-delay="50" data-tooltip="<?= htmlspecialchars(lang('user_profile_actions'), ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars(lang('user_profile_actions'), ENT_QUOTES, 'UTF-8') ?>">
                    <i class="material-icons" aria-hidden="true">more_vert</i>
                </a>
                <ul id="user-profile-actions" class="dropdown-content" role="menu">
                    <li v-if="profile.canUpdate"><a :href="base_url('admin/users/edit/' + user.user_id)"><?= lang('btn_edit') ?></a></li>
                    <li v-if="profile.isSelf"><a :href="base_url('admin/users/changePassword/' + user.user_id)"><?= lang('users_form_change_password') ?></a></li>
                    <li v-if="profile.canUpdate"><a href="#folderSelector" class="modal-trigger"><?= lang('user_profile_change_avatar') ?></a></li>
                    <li v-if="profile.canDeactivate && isActive"><a href="#!" v-on:click.prevent="setUserStatus(0)"><?= lang('user_profile_deactivate') ?></a></li>
                    <li v-if="profile.canDeactivate && !isActive"><a href="#!" v-on:click.prevent="setUserStatus(1)"><?= lang('user_profile_activate') ?></a></li>
                    <li v-if="profile.canDelete"><a href="#!" v-on:click.prevent="onRequestDelete"><?= lang('btn_delete') ?></a></li>
                </ul>
                <div class="card-image">
                    <img src="<?=base_url('public/img/profile/usertop.jpg');?>" alt="">
                </div>
                <div class="avatar">
                    <a v-if="profile.canUpdate" href="#folderSelector" class="modal-trigger">
                        <img v-if="user.user_data && user.user_data.avatar" :src="user.get_avatarurl()" :alt="user.username" class="circle z-depth-1">
                        <i v-else class="material-icons circle grey lighten-5 z-depth-1" aria-hidden="true">account_circle</i>
                    </a>
                    <span v-else>
                        <img v-if="user.user_data && user.user_data.avatar" :src="user.get_avatarurl()" :alt="user.username" class="circle z-depth-1">
                        <i v-else class="material-icons circle grey lighten-5 z-depth-1" aria-hidden="true">account_circle</i>
                    </span>
                </div>
                <div class="card-content user-profile-identity">
                    <div class="user-profile-identity__body">
                        <span class="card-title user-profile-identity__name">@{{ user.get_fullname() }}</span>
                        <p class="user-profile-username">
                            <i class="material-icons" aria-hidden="true">person</i>
                            <span>@{{ user.username }}</span>
                        </p>
                        <div class="user-profile-identity__chips">
                            <span class="user-profile-role" v-if="user.role">
                                <i class="material-icons" aria-hidden="true">supervisor_account</i>
                                <span>@{{ user.role }}</span>
                            </span>
                            <span class="user-profile-status" v-bind:class="isActive ? 'user-profile-status--active' : 'user-profile-status--inactive'">
                                <i class="material-icons" aria-hidden="true">@{{ isActive ? 'check_circle' : 'pause_circle_filled' }}</i>
                                @{{ isActive ? lang('user_profile_status_active') : lang('user_profile_status_inactive') }}
                            </span>
                        </div>
                        <p class="user-profile-lastseen" v-if="user.lastseen">
                            <i class="material-icons" aria-hidden="true">schedule</i>
                            <span><?= lang('users_last_seen') ?> · @{{ timeAgo(user.lastseen) }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="user-profile-panel user-profile-panel--contact" v-if="contactEmail || contactPhone || contactAddress">
                <div class="user-profile-panel__head">
                    <span class="user-profile-mark user-profile-mark--interactive" aria-hidden="true">
                        <i class="material-icons">contacts</i>
                    </span>
                    <span><?= lang('user_profile_contact') ?></span>
                </div>
                <a class="user-profile-contact__row" v-if="contactEmail" :href="'mailto:' + contactEmail">
                    <span class="user-profile-mark user-profile-mark--interactive" aria-hidden="true">
                        <i class="material-icons">email</i>
                    </span>
                    <span>@{{ contactEmail }}</span>
                </a>
                <a class="user-profile-contact__row" v-if="contactPhone" :href="'tel:' + contactPhone">
                    <span class="user-profile-mark user-profile-mark--success" aria-hidden="true">
                        <i class="material-icons">phone</i>
                    </span>
                    <span>@{{ contactPhone }}</span>
                </a>
                <div class="user-profile-contact__row" v-if="contactAddress">
                    <span class="user-profile-mark user-profile-mark--warning" aria-hidden="true">
                        <i class="material-icons">location_on</i>
                    </span>
                    <span>@{{ contactAddress }}</span>
                </div>
            </div>
            <div class="user-profile-panel user-profile-about" v-if="profileCargo || profileBio">
                <div class="user-profile-panel__head">
                    <span class="user-profile-mark user-profile-mark--accent" aria-hidden="true">
                        <i class="material-icons">info</i>
                    </span>
                    <span><?= lang('user_profile_about') ?></span>
                </div>
                <div class="user-profile-panel__body">
                    <p class="user-profile-about__cargo" v-if="profileCargo">
                        <i class="material-icons" aria-hidden="true">work</i>
                        <span>@{{ profileCargo }}</span>
                    </p>
                    <p class="user-profile-about__bio" v-if="profileBio">@{{ profileBio }}</p>
                </div>
            </div>
            <div class="user-profile-panel" v-if="breakdownRows.length">
                <div class="user-profile-panel__head">
                    <span class="user-profile-mark user-profile-mark--accent" aria-hidden="true">
                        <i class="material-icons">layers</i>
                    </span>
                    <span><?= lang('user_profile_breakdown') ?></span>
                </div>
                <template v-for="row in breakdownRows">
                    <a
                        v-if="row.can"
                        :key="'break-a-' + row.key"
                        class="user-profile-breakdown__row"
                        :href="base_url(row.href)"
                    >
                        <span class="user-profile-mark" :class="'user-profile-mark--' + row.tone" aria-hidden="true">
                            <i class="material-icons">@{{ row.icon }}</i>
                        </span>
                        <span class="user-profile-breakdown__label">@{{ lang(row.labelKey) }}</span>
                        <span class="user-profile-breakdown__count">@{{ row.count }}</span>
                    </a>
                    <div
                        v-else
                        :key="'break-d-' + row.key"
                        class="user-profile-breakdown__row"
                    >
                        <span class="user-profile-mark" :class="'user-profile-mark--' + row.tone" aria-hidden="true">
                            <i class="material-icons">@{{ row.icon }}</i>
                        </span>
                        <span class="user-profile-breakdown__label">@{{ lang(row.labelKey) }}</span>
                        <span class="user-profile-breakdown__count">@{{ row.count }}</span>
                    </div>
                </template>
            </div>
            <div class="user-profile-panel" v-if="showRecentFiles">
                <div class="user-profile-panel__head">
                    <span class="user-profile-mark user-profile-mark--chrome" aria-hidden="true">
                        <i class="material-icons">folder</i>
                    </span>
                    <span><?= lang('user_profile_recent_files') ?></span>
                </div>
                <div class="user-profile-files">
                    <a
                        v-for="file in summary.recent_files"
                        :key="file.file_id"
                        class="user-profile-files__item"
                        :href="fileUrl(file)"
                        :title="file.file_name"
                    >
                        <img v-if="isImageFile(file)" :src="fileUrl(file)" :alt="file.file_name">
                        <i v-else class="material-icons" aria-hidden="true">insert_drive_file</i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col s12 m7 l8 user-profile-main">
            <div class="user-profile-workspace">
                <div class="user-profile-tabs-wrap">
                    <ul class="tabs" id="user-tabs">
                        <li class="tab col s4">
                            <a class="active" href="#activity">
                                <i class="material-icons" aria-hidden="true">view_day</i>
                                <span><?= lang('user_profile_tab_activity') ?></span>
                            </a>
                        </li>
                        <li class="tab col s4">
                            <a href="#logs">
                                <i class="material-icons" aria-hidden="true">history</i>
                                <span><?= lang('user_profile_tab_logs') ?></span>
                            </a>
                        </li>
                        <li class="tab col s4">
                            <a href="#account">
                                <i class="material-icons" aria-hidden="true">person</i>
                                <span><?= lang('user_profile_tab_account') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="activity" class="user-profile-tab">
                    <div class="user-profile-panel user-profile-drafts" v-if="summary.drafts && summary.drafts.length">
                        <div class="user-profile-panel__head">
                            <span class="user-profile-mark user-profile-mark--warning" aria-hidden="true">
                                <i class="material-icons">edit</i>
                            </span>
                            <span><?= lang('user_profile_drafts') ?></span>
                        </div>
                        <div class="user-profile-drafts__scroll">
                            <a
                                class="user-profile-drafts__row"
                                v-for="draft in summary.drafts"
                                :key="'draft-' + draft.page_id"
                                :href="base_url('admin/pages/editar/' + draft.page_id)"
                            >
                                <span class="user-profile-mark user-profile-mark--warning" aria-hidden="true">
                                    <i class="material-icons">description</i>
                                </span>
                                <span class="user-profile-drafts__title">@{{ draft.title }}</span>
                                <i class="material-icons user-profile-drafts__chevron" aria-hidden="true">chevron_right</i>
                            </a>
                        </div>
                    </div>
                    <div class="user-profile-scroll js-timeline-scroll">
                        <div class="user-profile-empty" v-if="!timelineLoading && timelineDateKeys.length === 0">
                            <span class="user-profile-mark user-profile-mark--interactive" aria-hidden="true">
                                <i class="material-icons">view_day</i>
                            </span>
                            <p><?= lang('user_profile_activity_empty') ?></p>
                        </div>
                        <div class="user-profile-timeline" v-if="timelineDateKeys.length">
                            <div class="user-profile-timeline-day" v-for="day in timelineDateKeys" :key="day">
                                <div class="user-profile-timeline-day__label">@{{ day }}</div>
                                <div
                                    class="user-profile-timeline-item"
                                    v-for="element in timelineGroups[day]"
                                    :key="element.model_type + '-' + element.entity_id"
                                >
                                    <i class="material-icons user-profile-timeline-item__icon" v-bind:class="timelineIconClass(element)" aria-hidden="true">@{{ timelineIcon(element) }}</i>
                                    <div class="user-profile-timeline-item__body">
                                        <a v-if="timelineHref(element)" class="user-profile-timeline-item__title" :href="timelineHref(element)">@{{ element.title || '—' }}</a>
                                        <span v-else class="user-profile-timeline-item__title">@{{ element.title || '—' }}</span>
                                        <p class="user-profile-timeline-item__meta">
                                            @{{ timelineTypeLabel(element) }} · @{{ timeAgo(element.date_create) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="user-profile-tab__spinner" v-if="timelineLoading">
                            <preloader />
                        </div>
                        <div class="js-timeline-sentinel user-profile-sentinel"></div>
                    </div>
                </div>
                <div id="logs" class="user-profile-tab">
                    <div class="user-profile-scroll js-logs-scroll">
                        <div class="user-profile-empty" v-if="logsLoaded && !logsLoading && logs.length === 0 && !profile.loggerEnabled">
                            <span class="user-profile-mark user-profile-mark--chrome" aria-hidden="true">
                                <i class="material-icons">history</i>
                            </span>
                            <p><?= lang('user_profile_logs_disabled') ?></p>
                            <a v-if="profile.canSelectConfig" :href="base_url('admin/configuracion/')"><?= lang('user_profile_logs_open_config') ?></a>
                        </div>
                        <div class="user-profile-empty" v-else-if="logsLoaded && !logsLoading && logs.length === 0">
                            <span class="user-profile-mark user-profile-mark--chrome" aria-hidden="true">
                                <i class="material-icons">history</i>
                            </span>
                            <p><?= lang('user_profile_logs_empty') ?></p>
                        </div>
                        <ul class="user-profile-logs" v-if="logs.length">
                            <li class="user-profile-logs__item" v-for="row in logs" :key="'log-' + row.logger_id">
                                <span class="user-profile-chip" :class="logChipClass(row)">
                                    <i class="material-icons" aria-hidden="true">@{{ logIcon(row) }}</i>
                                    @{{ row.type }}
                                </span>
                                <span class="user-profile-logs__token">@{{ row.token }}</span>
                                <a v-if="row.type_link" class="user-profile-logs__comment" :href="base_url(row.type_link)" :title="row.comment">@{{ truncateText(row.comment, 140) }}</a>
                                <span v-else class="user-profile-logs__comment" :title="row.comment">@{{ truncateText(row.comment, 140) }}</span>
                                <span class="user-profile-logs__time">@{{ timeAgo(row.date_create) }}</span>
                            </li>
                        </ul>
                        <div class="user-profile-tab__spinner" v-if="logsLoading">
                            <preloader />
                        </div>
                        <div class="js-logs-sentinel user-profile-sentinel"></div>
                    </div>
                </div>
                <div id="account" class="user-profile-tab">
                    <div class="user-profile-scroll">
                        <div class="user-profile-panel user-profile-panel--flush">
                            <div class="user-profile-panel__head">
                                <span class="user-profile-mark user-profile-mark--chrome" aria-hidden="true">
                                    <i class="material-icons">person</i>
                                </span>
                                <span><?= lang('user_profile_tab_account') ?></span>
                            </div>
                            <div class="user-profile-meta__row" v-if="user.role">
                                <span class="user-profile-mark user-profile-mark--accent" aria-hidden="true">
                                    <i class="material-icons">supervisor_account</i>
                                </span>
                                <div class="user-profile-meta__body">
                                    <span class="user-profile-meta__label"><?= lang('users_role') ?></span>
                                    <a class="user-profile-meta__value" v-if="profile.canUpdateUsergroup && profile.groupEditUrl" :href="profile.groupEditUrl">@{{ user.role }}</a>
                                    <span class="user-profile-meta__value" v-else>@{{ user.role }}</span>
                                </div>
                            </div>
                            <div class="user-profile-meta__row" v-if="user.date_create">
                                <span class="user-profile-mark user-profile-mark--interactive" aria-hidden="true">
                                    <i class="material-icons">event</i>
                                </span>
                                <div class="user-profile-meta__body">
                                    <span class="user-profile-meta__label"><?= lang('creation_date') ?></span>
                                    <span class="user-profile-meta__value">@{{ timeAgo(user.date_create) }}</span>
                                </div>
                            </div>
                            <div class="user-profile-meta__row" v-if="user.lastseen">
                                <span class="user-profile-mark user-profile-mark--chrome" aria-hidden="true">
                                    <i class="material-icons">schedule</i>
                                </span>
                                <div class="user-profile-meta__body">
                                    <span class="user-profile-meta__label"><?= lang('users_last_seen') ?></span>
                                    <span class="user-profile-meta__value">@{{ timeAgo(user.lastseen) }}</span>
                                </div>
                            </div>
                            <div class="user-profile-meta__row" v-if="summary.last_login">
                                <span class="user-profile-mark user-profile-mark--success" aria-hidden="true">
                                    <i class="material-icons">vpn_key</i>
                                </span>
                                <div class="user-profile-meta__body">
                                    <span class="user-profile-meta__label"><?= lang('user_profile_last_login') ?></span>
                                    <span class="user-profile-meta__value">@{{ timeAgo(summary.last_login) }}</span>
                                </div>
                            </div>
                            <div class="user-profile-permissions" v-if="summary.permissions && summary.permissions.length">
                                <div class="user-profile-panel__head user-profile-panel__head--nested">
                                    <span class="user-profile-mark user-profile-mark--interactive" aria-hidden="true">
                                        <i class="material-icons">verified_user</i>
                                    </span>
                                    <span><?= lang('user_profile_permissions') ?></span>
                                </div>
                                <div class="user-profile-permissions__chips">
                                    <span class="user-profile-chip user-profile-chip--interactive" v-for="perm in summary.permissions" :key="perm">@{{ perm }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <file-explorer-selector
        :uploader="'single'"
        :preselected="[]"
        :modal="'folderSelector'"
        :mode="'files'"
        :filter="'images'"
        :multiple="false"
        v-on:notify="uploadCallback"
        :initialdir="'./public/img/profile/' + (user.username || '') + '/'"
    ></file-explorer-selector>
    <confirm-modal
        id="deleteModal"
        title="<?= htmlspecialchars(lang('confirm_delete'), ENT_QUOTES, 'UTF-8') ?>"
        v-on:notify="confirmCallback"
    >
        <p><?= htmlspecialchars(lang('users_confirm_delete'), ENT_QUOTES, 'UTF-8') ?></p>
    </confirm-modal>
</div>
@include('admin.components.file_explorer_selector_component')
@endsection

@section('footer_includes')
<script>
window.USER_PROFILE = <?= json_encode($profile) ?>;
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, {
    user_profile_tab_activity: <?= json_encode(lang('user_profile_tab_activity')) ?>,
    user_profile_tab_logs: <?= json_encode(lang('user_profile_tab_logs')) ?>,
    user_profile_tab_account: <?= json_encode(lang('user_profile_tab_account')) ?>,
    user_profile_activity_empty: <?= json_encode(lang('user_profile_activity_empty')) ?>,
    user_profile_logs_empty: <?= json_encode(lang('user_profile_logs_empty')) ?>,
    user_profile_logs_disabled: <?= json_encode(lang('user_profile_logs_disabled')) ?>,
    user_profile_logs_open_config: <?= json_encode(lang('user_profile_logs_open_config')) ?>,
    user_profile_drafts: <?= json_encode(lang('user_profile_drafts')) ?>,
    user_profile_kpi_pages: <?= json_encode(lang('user_profile_kpi_pages')) ?>,
    user_profile_kpi_collections: <?= json_encode(lang('user_profile_kpi_collections')) ?>,
    user_profile_kpi_items: <?= json_encode(lang('user_profile_kpi_items')) ?>,
    user_profile_kpi_files: <?= json_encode(lang('user_profile_kpi_files')) ?>,
    user_profile_type_page: <?= json_encode(lang('user_profile_type_page')) ?>,
    user_profile_type_collection: <?= json_encode(lang('user_profile_type_collection')) ?>,
    user_profile_type_item: <?= json_encode(lang('user_profile_type_item')) ?>,
    user_profile_deactivate: <?= json_encode(lang('user_profile_deactivate')) ?>,
    user_profile_activate: <?= json_encode(lang('user_profile_activate')) ?>,
    user_profile_change_avatar: <?= json_encode(lang('user_profile_change_avatar')) ?>,
    user_profile_last_login: <?= json_encode(lang('user_profile_last_login')) ?>,
    user_profile_permissions: <?= json_encode(lang('user_profile_permissions')) ?>,
    user_profile_breakdown: <?= json_encode(lang('user_profile_breakdown')) ?>,
    user_profile_recent_files: <?= json_encode(lang('user_profile_recent_files')) ?>,
    user_profile_status_active: <?= json_encode(lang('user_profile_status_active')) ?>,
    user_profile_status_inactive: <?= json_encode(lang('user_profile_status_inactive')) ?>,
    user_profile_deactivated: <?= json_encode(lang('user_profile_deactivated')) ?>,
    user_profile_activated: <?= json_encode(lang('user_profile_activated')) ?>,
    menu_fragments: <?= json_encode(lang('menu_fragments')) ?>,
    menu_albums: <?= json_encode(lang('menu_albums')) ?>,
    menu_events: <?= json_encode(lang('menu_events')) ?>,
    menu_menus: <?= json_encode(lang('menu_menus')) ?>,
    menu_siteforms: <?= json_encode(lang('menu_siteforms')) ?>
});
</script>
<script src="{{base_url('resources/components/FileExplorerSelector.js?v=' . ADMIN_VERSION)}}"></script>
<script src="<?=base_url('resources/components/UserProfileComponent.js?v=' . ADMIN_VERSION);?>"></script>
@endsection
