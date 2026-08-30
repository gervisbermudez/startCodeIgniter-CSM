<script type="text/x-template" id="FormSiteDetails-template">
    <div id="FormSiteDetails-root" class="container">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <br><br>
            <preloader />
        </div>
        <div class="form-site-details z-depth-1" v-cloak v-if="!loader">
            <header class="submit-details-head">
                <div class="submit-details-head__main">
                    <p class="page-header">{{ lang('siteforms_submission') }} #@{{ submitId }}</p>
                    <p class="submit-details-head__meta">
                        <span v-if="formName">@{{ formName }}</span>
                        <span v-if="formName && data.date_create" class="submit-details-head__dot" aria-hidden="true">·</span>
                        <time v-if="data.date_create" :datetime="data.date_create" :title="data.date_create">@{{ timeAgo(data.date_create) }}</time>
                    </p>
                </div>
                <span class="custom-badge" :class="isNew ? 'submit-status-new' : 'submit-status-seen'">
                    <i class="material-icons">@{{ isNew ? 'markunread' : 'done' }}</i>
                    @{{ statusLabel }}
                </span>
            </header>
            <ul class="tabs tabs-fixed-width" id="formTabs" role="tablist">
                <li class="tab">
                    <a class="active" href="#details">
                        <i class="material-icons" aria-hidden="true">assignment</i>
                        <span>{{ lang('siteforms_details') }}</span>
                    </a>
                </li>
                <li v-if="data.user_tracking_id" class="tab">
                    <a href="#tracking">
                        <i class="material-icons" aria-hidden="true">assignment_ind</i>
                        <span>{{ lang('siteforms_tracking') }}</span>
                    </a>
                </li>
                <li class="tab">
                    <a href="#form">
                        <i class="material-icons" aria-hidden="true">description</i>
                        <span>{{ lang('siteforms_form') }}</span>
                    </a>
                </li>
            </ul>
            <div id="details" class="submit-details-pane">
                <div class="row">
                    <div class="col s12 l8">
                        <div v-if="!keys.length" class="submit-empty">
                            <i class="material-icons" aria-hidden="true">inbox</i>
                            <p>{{ lang('siteforms_no_fields') }}</p>
                        </div>
                        <dl v-else class="submit-dl">
                            <div class="submit-dl__row" v-for="(key, index) in keys" :key="index" :class="{ 'submit-dl__row--long': isLongField(key) }">
                                <dt>@{{ fieldLabel(key) }}</dt>
                                <dd>
                                    <a v-if="isEmail(fieldValue(key))" :href="'mailto:' + fieldValue(key)">@{{ fieldValue(key) }}</a>
                                    <span v-else-if="!fieldValue(key)" class="submit-dl__empty">—</span>
                                    <span v-else :class="{ 'submit-dl__long': isLongField(key) }">@{{ fieldValue(key) }}</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <aside class="col s12 l4">
                        <dl class="submit-aside">
                            <div class="submit-aside__item">
                                <dt>{{ lang('siteforms_form') }}</dt>
                                <dd>@{{ formName || '—' }}</dd>
                            </div>
                            <div class="submit-aside__item">
                                <dt>{{ lang('siteforms_created') }}</dt>
                                <dd>
                                    <time v-if="data.date_create" :datetime="data.date_create" :title="data.date_create">@{{ timeAgo(data.date_create) }}</time>
                                    <span v-else>—</span>
                                </dd>
                            </div>
                            <div class="submit-aside__item">
                                <dt>{{ lang('siteforms_status') }}</dt>
                                <dd>
                                    <span class="custom-badge" :class="isNew ? 'submit-status-new' : 'submit-status-seen'">
                                        @{{ statusLabel }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </aside>
                </div>
            </div>
            <div id="tracking" class="submit-details-pane" v-if="data.user_tracking_id">
                <dl class="submit-dl">
                    <div class="submit-dl__row">
                        <dt>{{ lang('siteforms_client_ip') }}</dt>
                        <dd class="submit-dl__value-row">
                            <span>@{{ tracking.client_ip || '—' }}</span>
                            <button
                                v-if="tracking.client_ip"
                                type="button"
                                class="btn-flat submit-copy tooltipped"
                                data-position="top"
                                :data-tooltip="t('copy')"
                                :aria-label="t('copy')"
                                v-on:click="copyText(tracking.client_ip)">
                                <i class="material-icons" aria-hidden="true">content_copy</i>
                            </button>
                        </dd>
                    </div>
                    <div class="submit-dl__row">
                        <dt>{{ lang('siteforms_created') }}</dt>
                        <dd>
                            <time v-if="tracking.date_create" :datetime="tracking.date_create" :title="tracking.date_create">@{{ timeAgo(tracking.date_create) }}</time>
                            <span v-else>—</span>
                        </dd>
                    </div>
                    <div class="submit-dl__row">
                        <dt>{{ lang('siteforms_visits') }}</dt>
                        <dd>@{{ tracking.no_of_visits || '—' }}</dd>
                    </div>
                    <div class="submit-dl__row submit-dl__row--long">
                        <dt>{{ lang('siteforms_user_agent') }}</dt>
                        <dd>
                            <span v-if="tracking.user_agent" class="submit-dl__long">@{{ tracking.user_agent }}</span>
                            <span v-else class="submit-dl__empty">—</span>
                        </dd>
                    </div>
                </dl>
            </div>
            <div id="form" class="submit-details-pane">
                <div class="row">
                    <div class="col s12 l8">
                        <dl class="submit-dl">
                            <div class="submit-dl__row">
                                <dt>{{ lang('siteforms_name') }}</dt>
                                <dd>@{{ formName || '—' }}</dd>
                            </div>
                            <div class="submit-dl__row">
                                <dt>{{ lang('siteforms_created') }}</dt>
                                <dd>
                                    <time v-if="data.siteform && data.siteform.date_create" :datetime="data.siteform.date_create" :title="data.siteform.date_create">@{{ timeAgo(data.siteform.date_create) }}</time>
                                    <span v-else>—</span>
                                </dd>
                            </div>
                            <div class="submit-dl__row">
                                <dt>{{ lang('siteforms_template') }}</dt>
                                <dd>@{{ (data.siteform && data.siteform.template) || '—' }}</dd>
                            </div>
                            <div class="submit-dl__row">
                                <dt>{{ lang('siteforms_status') }}</dt>
                                <dd>
                                    <span v-if="formIsActive" class="custom-badge status-published">{{ lang('siteforms_active') }}</span>
                                    <span v-else class="custom-badge status-draft">{{ lang('siteforms_inactive') }}</span>
                                </dd>
                            </div>
                            <div class="submit-dl__row submit-dl__row--long">
                                <dt>{{ lang('siteforms_created_by') }}</dt>
                                <dd>
                                    <userInfo v-bind:user="user" />
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="submit-details-actions">
                <a href="#!" class="btn-flat waves-effect" v-on:click.prevent="back">
                    <i class="material-icons left" aria-hidden="true">arrow_back</i>{{ lang('siteforms_back_to_list') }}
                </a>
                <a
                    v-if="isNew"
                    href="#!"
                    class="btn waves-effect waves-light"
                    :class="{ disabled: archiving }"
                    v-on:click.prevent="setArchive">
                    <i class="material-icons left" aria-hidden="true">assignment_turned_in</i>{{ lang('siteforms_mark_seen') }}
                </a>
                <a
                    v-if="data.siteform && data.siteform.siteform_id"
                    :href="base_url('admin/siteforms/editar/' + data.siteform.siteform_id)"
                    class="btn-flat waves-effect">
                    <i class="material-icons left" aria-hidden="true">edit</i>{{ lang('siteforms_edit_form') }}
                </a>
            </div>
        </div>
    </div>
</script>
