<script type="text/x-template" id="albumes-widget-template">
    <div class="panel albumes">
		<div class="title green">
			<h5>Albumes</h5>
			<div class="subtitle">
				@{{albumes.length}} Albumes
			</div>
		</div>
		<div class="panel-boddy">
			<div class="row">
				<div class="col s4" v-for="(album, index) in albumes" :key="index">
                    <div class="card album">
                        <a :href="base_url('admin/galeria/items/' + album.album_id)" class="card-image">
                            <div class="card-image-container">
                                <img :src="getPageImagePath(album, 0)" class="bottom"/>
                                <img :src="getPageImagePath(album, 1)" class="top"/>
                            </div>
                        </a>
                        <div class="card-content">
                            <span class="card-title"><a :href="base_url('admin/galeria/items/' + album.album_id)">@{{album.name}}</a></span>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</script>
