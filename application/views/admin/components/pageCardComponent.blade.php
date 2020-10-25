<script type="text/x-template" id="page-card-template">
    <div class="panel">
		<div class="title indigo">
			<h5>Pages</h5>
			<div class="subtitle">
				@{{pages.length}} Pages
			</div>
		</div>
		<div class="panel-boddy">
			<div class="row">
				<div class="col s12 m4" v-for="(page, index) in pages" :key="index" v-if="index < 3">
					<div class="page-widget">
						<div class="m-portlet m-portlet--bordered-semi m-portlet--full-height  m-portlet--rounded-force">
							<div class="m-portlet__head m-portlet__head--fit">
								<div class="m-portlet__head-caption">
									<div class="m-portlet__head-action">
										<button type="button" class="btn btn-sm m-btn--pill btn-brand">@{{page.page_type_name}}</button>
									</div>
								</div>
							</div>
							<div class="m-portlet__body">
								<div class="m-widget19">
									<div class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides"
										style="min-height-: 286px">
										<img v-if="page.imagen_file" :src="'/' + page.imagen_file.file_front_path" :alt="page.title">
										<img v-else src="/public/img/default.jpg" :alt="page.title">
										<h3 class="m-widget19__title m--font-light truncate">
											<a :href="getPageFullPath(page)" class="white-text">@{{page.title}}</a>
										</h3>
										<div class="m-widget19__shadow"></div>
									</div>
									<div class="m-widget19__content">
										<div class="m-widget19__header">
											<div class="m-widget19__user-img">
												<img class="m-widget19__img" :src="page.user.get_avatarurl()" alt="">
											</div>
											<div class="m-widget19__info">
												<a class="m-widget19__username" :href="page.user.get_profileurl()">
												@{{page.user.get_fullname()}}
												</a><br>
												<span class="m-widget19__time">
												@{{page.user.usergroup.name}}
												</span>
											</div>
											<div class="m-widget19__stats">
												<span class="m-widget19__number m--font-brand">
												<i v-if="page.status == 1" class="material-icons tooltipped" data-position="left"
													data-delay="50" data-tooltip="Publicado">publish</i>
												<i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
													data-tooltip="Borrador">edit</i>
												</span>
												<span v-if="page.status == 1" class="m-widget19__comment">
												Publicado
												</span>
												<span v-else class="m-widget19__comment">
												Borrador
												</span>
											</div>
										</div>
										<div class="m-widget19__body"  v-html="contentText(page)"></div>
									</div>
									<div class="m-widget19__action">
											<a :href="getPageFullPath(page)" class="btn m-btn--pill btn-secondary m-btn m-btn--hover-brand m-btn--custom">
											<span>
												View
												</span>
											</a>
											<a :href="getPageEditPath(page)" class="btn m-btn--pill btn-secondary m-btn m-btn--hover-brand m-btn--custom">
												<span>
												Edit
												</span>
											</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>
