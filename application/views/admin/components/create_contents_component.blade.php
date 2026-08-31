@include('admin.custommodels.i18n')
<?php $dropdownid = random_string('alpha', 16)?>
<script type="text/x-template" id="create-contents-template">
    <div class="panel">
	<div class="title">
		<h5><?= lang('dashboard_latest_collection_items') ?></h5>
		<div class="subtitle">
			@{{content.length}} <?= lang('dashboard_collection_items_total') ?>
		</div>
		<img src="{{base_url()}}public/img/admin/dashboard/undraw_browsing_online_sr8c.png" />
	</div>
	<div class="contents row">
		<div class="col s12" v-for="(item, index) in content" :key="item.custom_model_content_id || index" v-if="index < 5">
		<table class="">
			<tbody>
			<tr>
				<td>
					<span>@{{ item.title || item.custom_model_content_id }}</span>
				</td>
				<td><span class="new badge" data-badge-caption="">@{{item.custom_model && item.custom_model.form_name}}</span></td>
				<td v-if="item.user">@{{item.user.get_fullname()}}</td>
				<td>@{{timeAgo(item.date_create)}}</td>
				<td>
				<div class="switch">
					<label>
					<?= lang('collections_draft') ?>
					<input type="checkbox" :checked="item.status == 1 || item.status == '1'" @change="toggleStatus(item, $event)">
					<span class="lever"></span>
					<?= lang('collections_published') ?>
					</label>
				</div>
				</td>
			</tr>
			</tbody>
		</table>
		</div>
	</div>
	<p><a href="{{ base_url('admin/custommodels/') }}"><?= lang('collections_view_all') ?></a></p>
</div>
</script>
