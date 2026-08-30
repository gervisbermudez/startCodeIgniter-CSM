<script type="text/x-template" id="FormSiteDetails-template">
    <div id="FormSiteDetails-root" class="container">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <br><br>
            <preloader />
        </div>
        <div class="form form-site-details" v-cloak v-if="!loader">
            <div class="row">
                <div class="col s12">
                    <ul class="tabs" id="formTabs">
                        <li class="tab col s3"><a class="active" href="#details"><i class="material-icons">assignment</i>
                                {{ lang('siteforms_details') }}</a></li>
                        <li v-if="data.user_tracking_id" class="tab col s3"><a href="#tracking"><i class="material-icons">assignment_ind</i> {{ lang('siteforms_tracking') }}</a>
                        </li>
                        <li class="tab col s3"><a href="#form"><i class="material-icons">description</i> {{ lang('siteforms_form') }}</a>
                        </li>
                    </ul>
                </div>
                <div id="details" class="row">
                    <div class="col s8">
                        <div class="col s12" v-for="(key, index) in keys" :key="index">
                            <b>@{{key | capitalize}}:</b> <br />
                            <div>
                                @{{data.siteform_submit_data[key]}}
                            </div>
                        <br />
                        </div>
                        <div class="row" v-cloak v-if="!loader">
                            <div class="col s12">
                                <br />
                                <a v-if="data.status == 1" v-on:click="setArchive" class="waves-effect waves-light btn"><i
                                        class="material-icons left">assignment_turned_in</i>{{ lang('siteforms_mark_seen') }}</a>
                                <a v-if="data.status == 2" class="waves-effect waves-light btn disabled"><i
                                    class="material-icons left">check</i>{{ lang('siteforms_seen') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col s4">
                        <div class="col s12">
                            <b>{{ lang('siteforms_form') }}:</b> <br />
                            <div>
                                @{{data.siteform && data.siteform.name}}
                            </div>
                            <br />
                        </div>
                        <div class="col s12">
                            <b>{{ lang('siteforms_created') }}</b> <br />
                            <div>
                                @{{timeAgo(data.date_create)}}
                            </div>
                            <br />
                        </div>
                    </div>
                </div>
                <div id="tracking" class="row" v-if="data.user_tracking_id">
                    <div class="col s12">
                        <b>{{ lang('siteforms_client_ip') }}:</b> <br />
                        <div>
                            @{{data.user_tracking && data.user_tracking.client_ip}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>{{ lang('siteforms_created') }}</b> <br />
                        <div>
                            @{{data.user_tracking && timeAgo(data.user_tracking.date_create)}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>{{ lang('siteforms_visits') }}</b> <br />
                        <div>
                            @{{data.user_tracking && data.user_tracking.no_of_visits}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>{{ lang('siteforms_user_agent') }}</b> <br />
                        <div>
                            @{{data.user_tracking && data.user_tracking.user_agent}}
                        </div>
                        <br />
                    </div>
                </div>
                <div class="row" id="form">
                    <div class="col s12">
                        <b>{{ lang('siteforms_name') }}:</b> <br />
                        <div>
                            @{{data.siteform && data.siteform.name}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>{{ lang('siteforms_created') }}:</b> <br />
                        <div>
                            @{{data.siteform && timeAgo(data.siteform.date_create)}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>{{ lang('siteforms_template') }}:</b> <br />
                        <div>
                            @{{data.siteform && data.siteform.template}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>{{ lang('siteforms_status') }}:</b> <br />
                        <div>
                            <span v-if="data.siteform && parseInt(data.siteform.status, 10) === 1">{{ lang('siteforms_active') }}</span>
                            <span v-else>{{ lang('siteforms_inactive') }}</span>
                        </div>
                    </div>
                    <div class="col s12">
                        <p><b>{{ lang('siteforms_created_by') }}</b>:</p>
                        <userInfo v-bind:user="user" />
                        <br /><br />
                    </div>
                    <div class="col s12">
                        <a v-if="data.siteform" :href="base_url('admin/siteforms/editar/' + data.siteform.siteform_id)" class="waves-effect waves-light btn"><i class="material-icons left">edit</i>{{ lang('siteforms_edit_form') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-cloak v-if="!loader">
            <div class="col s12">
                <br />
                <a v-on:click="back" class="waves-effect waves-light btn"><i
                        class="material-icons left">arrow_back</i>{{ lang('siteforms_back_to_list') }}</a>
            </div>
        </div>
    </div>
</script>
