<?php $dropdownid = random_string('alpha', 16)?>
<script type="text/x-template" id="create-contents-template">
    <div class="panel">
	<div class="title">
		<h5>Latest Contents</h5>
		<div class="subtitle">
			@{{content.length}} Total contents
		</div>
		<img src="{{base_url()}}public/img/admin/dashboard/undraw_browsing_online_sr8c.png" />
	</div>
	<div class="contents row">
		<div class="col s12" v-for="(item, index) in content" :key="index" v-if="index < 3">
		<table class="">
			<tbody>
			<tr>
				<td>
					<ul>
						<li class="collection-item" v-for="(value, key) in Object.values(item.data)" :key="key" v-if="key < 2">@{{ value }}</li>
					</ul>
				</td>
				<td><span class="new badge" data-badge-caption="">@{{item.custom_model.form_name}}</span></td>
				<td>@{{item.user.get_fullname()}}</td>
				<td>@{{timeAgo(item.date_create)}}</td>
				<td>
				<div class="switch">
					<label>
					Inactive
					<input type="checkbox" v-model="item.status" @change="toggleStatus(item);">
					<span class="lever"></span>
					Active
					</label>
				</div>
				</td>
			</tr>
			</tbody>
		</table>
		</div>
	</div>
</div>
</script>
