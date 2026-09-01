@include('admin.custommodels.i18n')
<script type="text/x-template" id="create-contents-template">
    <div class="panel dashboard-list-panel dash-collections">
        <div class="title panel-title-row">
            <h5><?= lang('dashboard_latest_collection_items') ?></h5>
            <a href="{{ base_url('admin/custommodels/') }}" class="btn-flat waves-effect teal-text"><?= lang('collections_view_all') ?></a>
        </div>
        <p class="dash-collections__count">
            @{{typeof total === 'number' ? total : content.length}} <?= lang('dashboard_collection_items_total') ?>
        </p>
        <ul class="dash-collections__list">
            <li v-for="(item, index) in content" :key="item.custom_model_content_id || index" v-if="index < 5" class="dash-collections__item">
                <a :href="itemUrl(item)" class="dash-collections__main">
                    <span class="dash-collections__title truncate">@{{ item.title || item.custom_model_content_id }}</span>
                    <span class="dash-collections__meta">
                        <span class="dash-collections__chip">@{{ item.custom_model && item.custom_model.form_name }}</span>
                        <span v-if="item.user">@{{ item.user.get_fullname ? item.user.get_fullname() : '' }}</span>
                        <span>@{{ timeAgo(item.date_create) }}</span>
                    </span>
                </a>
                <div class="switch dash-collections__switch">
                    <label>
                        <?= lang('collections_draft') ?>
                        <input type="checkbox" :checked="item.status == 1 || item.status == '1' || item.status === true" @change="toggleStatus(item, $event)">
                        <span class="lever"></span>
                        <?= lang('collections_published') ?>
                    </label>
                </div>
            </li>
        </ul>
        <div v-if="!content.length" class="dashboard-empty">
            <i class="material-icons">view_module</i>
            <p><?= lang('dashboard_latest_collection_items') ?></p>
            @if(has_permisions('CREATE_FORM_CUSTOM'))
            <a href="{{ base_url('admin/custommodels/new') }}" class="btn-small waves-effect waves-light"><?= lang('collections_view_all') ?></a>
            @endif
        </div>
    </div>
</script>
