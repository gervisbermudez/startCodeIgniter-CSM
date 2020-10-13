<script type="text/x-template" id="user-collection-template">
    <ul class="collection">
	<li class="collection-header collection-item avatar">
		<h5>Usuarios</h5>
		<p>
			@{{users.length}} Usuarios totales
		</p>
	</li>
	<li class="collection-item avatar" v-for="(user, index) in users" :key="index">
		<a :href="user.get_profileurl()">
			<img :src="user.get_avatarurl()" :alt="user.get_fullname()" class="circle">
			<span class="title">@{{user.get_fullname()}}</span>
			<p>@{{user.role}}</p>
		</a>
		<a :href="user.get_edit_url()" class="secondary-content"><i class="material-icons">edit</i></a>
	</li>
</ul>
</script>
