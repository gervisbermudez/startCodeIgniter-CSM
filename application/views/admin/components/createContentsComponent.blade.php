<?php $dropdownid = random_string('alpha', 16)?>
<script type="text/x-template" id="create-contents-template">
    <div class="panel">
	<div class="title">
		<h5>Contenidos Creados</h5>
		<div class="subtitle">
			@{{content.length}} Contenidos creados
		</div>
        <a data-position="left" data-delay="50" data-tooltip="Crear contenido"
            class='tooltipped dropdown-trigger btn right btn-floating halfway-fab waves-effect waves-light'
			href="{{base_url('admin/formularios/nuevo')}}" data-target='{{$dropdownid}}'>
			<i class="large material-icons">add</i>
		</a>
		<ul id='{{$dropdownid}}' class='dropdown-content'>
			<li v-for="(item, index) in forms_types" :key="index">
			<a :href="getFormsTypeUrl(item)" :title="item.form_description">@{{item.form_name}}</a></li>
		</ul>
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
				<td><span class="new badge" data-badge-caption="">@{{item.form_custom.form_name}}</span></td>
				<td>@{{item.user.get_fullname()}}</td>
				<td>@{{timeAgo(item.date_create)}}</td>
				<td>
				<div class="switch">
					<label>
					Inactivo
					<input type="checkbox" v-model="item.status" @change="toggleStatus(item);">
					<span class="lever"></span>
					Activo
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
