<script type="text/x-template" id="user-collection-template">
    <ul class="collection">
	<li class="collection-header collection-item avatar">
		<h5>{{ lang('dashboard_users_title') }}</h5>
		<p class="sub-header">
			@{{typeof total === 'number' ? total : users.length}} {{ lang('dashboard_users_total') }}
		</p>
	</li>
	<li class="collection-item avatar" v-for="(user, index) in users" :key="index">
		<a :href="user.get_profileurl()">
			<img :src="user.get_avatarurl()" :alt="user.get_fullname()" class="circle">
			<span class="title">@{{user.get_fullname()}}</span>
			<p>@{{user.role}}</p>
		</a>
		<a :href="user.get_edit_url()" class="secondary-content"><i class="material-icons">more_vert</i></a>
	</li>
	<li v-if="users.length === 0" class="collection-item center-align" style="padding: 40px 20px;">
		<i class="material-icons" style="font-size: 48px; color: #9e9e9e;">people_outline</i>
		<p style="color: #9e9e9e; margin-top: 10px;">{{ lang('dashboard_users_empty') }}</p>
		@if(has_permisions('CREATE_USER'))
		<a href="{{base_url('admin/users/agregar')}}" class="btn-small waves-effect waves-light" style="margin-top: 10px;">{{ lang('dashboard_users_empty_cta') }}</a>
		@endif
	</li>
</ul>
</script>
