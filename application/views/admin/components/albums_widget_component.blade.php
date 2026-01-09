<script type="text/x-template" id="albumes-widget-template">
    <div class="panel albumes">
		<div class="title">
			<h5>Your Albums</h5>
			<div class="subtitle sub-header">
				@{{albumes.length}} Albums
			</div>
			<img src="{{base_url()}}public/img/admin/dashboard/undraw_Photo_re_5blb.png" />
		</div>
		<div class="panel-boddy">
			<div class="row">
				<div class="col s12" v-for="(album, index) in albumes" :key="index">
                    <div class="card album">
                        <a :href="base_url('admin/gallery/items/' + album.album_id)" class="card-image">
                            <div class="card-image-container">
                                <img :src="getPageImagePath(album, 0)" class="bottom"/>
                                <img :src="getPageImagePath(album, 1)" class="top"/>
                            </div>
                        </a>
                        <div class="card-content">
                            <span class="card-title"><a :href="base_url('admin/gallery/items/' + album.album_id)">@{{album.name}}</a></span>
                        </div>
                    </div>
				</div>
				<div v-if="albumes.length === 0" class="col s12 center-align" style="padding: 60px 20px;">
					<i class="material-icons" style="font-size: 64px; color: #9e9e9e;">photo_library</i>
					<p style="color: #9e9e9e; margin-top: 15px; font-size: 16px;">No albums created yet</p>
					<a href="{{base_url('admin/gallery/nuevo')}}" class="btn waves-effect waves-light" style="margin-top: 15px;">Create First Album</a>
				</div>
			</div>
		</div>
	</div>
</script>
