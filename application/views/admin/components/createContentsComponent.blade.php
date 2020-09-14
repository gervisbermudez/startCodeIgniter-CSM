<?php $dropdownid = random_string('alpha', 16)?>
<script type="text/x-template" id="create-contents-template">
    <div class="panel">
	<div class="title">
		<h5>Contenidos Creados</h5>
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
	<div class="content row">
		<div class="col s12 m4" v-for="(item, index) in content" :key="index" v-if="index < 3">
			<div class="card">
				<ul class="collection">
					<li class="collection-item" v-for="(value, key) in Object.values(item.data)" :key="key" v-if="key < 3">@{{ value }}</li>
				</ul>
				<div class="card-action">
					<a :href="base_url('admin/usuarios/ver/' + item.user.user_id)">@{{item.user.username}}</a>
					<a href="#">@{{item.form_custom.form_name}}</a>
				</div>
			</div>
		</div>
	</div>
</div>
</script>
