<script type="text/x-template" id="user-collection-template">
<ul class="collection">
	<li class="collection-header collection-item avatar">
		<h5>Usuarios</h5>
	</li>
	<li class="collection-item avatar" v-for="(user, index) in users" :key="index">
		<a :href="user.get_profileurl()">
			<img :src="user.get_avatarurl()" alt="" class="circle">
			<span class="title">@{{user.get_fullname()}}</span>
			<p>@{{user.role}}</p>
		</a>
	</li>
</ul>
</script>
