@include('admin.custommodels.i18n')
<script type="text/x-template" id="create-contents-template">
    <div class="dash-list-widget dash-collections has-deco has-deco--wide" style="--dash-deco: url('{{ base_url('public/img/admin/undraw/undraw_selected-box_qnrz.svg') }}')">
        <div class="dash-widget-head">
            <div class="dash-widget-head__lead">
                <span class="dash-widget-glyph" aria-hidden="true"><i class="material-icons">view_module</i></span>
                <h5><a href="{{ base_url('admin/custommodels/') }}"><?= lang('dashboard_latest_collection_items') ?></a></h5>
            </div>
            <div class="dash-widget-head__tools">
                <span class="dash-widget-head__count">@{{typeof total === 'number' ? total : content.length}} <?= lang('dashboard_collection_items_total') ?></span>
                @include('admin.components.dash_widget_add', [
                    'perm' => 'CREATE_FORM_CUSTOM',
                    'href' => base_url('admin/custommodels/new'),
                    'tip' => lang('tooltip_new_collection'),
                ])
            </div>
        </div>
        <ul class="dash-list">
            <li v-for="(item, index) in content" :key="item.custom_model_content_id || index" v-if="index < 5">
                <a :href="itemUrl(item)" class="dash-list__row dash-list__row--stack">
                    <span class="dash-list__title truncate">@{{ item.title || item.custom_model_content_id }}</span>
                    <span class="dash-list__meta">
                        <span class="dash-collections__chip">@{{ item.custom_model && item.custom_model.form_name }}</span>
                        <span v-if="item.user">@{{ item.user.get_fullname ? item.user.get_fullname() : '' }}</span>
                        <span>@{{ timeAgo(item.date_create) }}</span>
                    </span>
                </a>
            </li>
        </ul>
        <div v-if="!content.length" class="dashboard-empty">
            <i class="material-icons">view_module</i>
            <p><?= lang('dashboard_latest_collection_items') ?></p>
        </div>
    </div>
</script>
