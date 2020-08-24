<script type="text/x-template" id="page-card-template">
	<div class="page-widget">
		<div class="m-portlet m-portlet--bordered-semi m-portlet--full-height  m-portlet--rounded-force">
			<div class="m-portlet__head m-portlet__head--fit">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-action">
						<button type="button" class="btn btn-sm m-btn--pill  btn-brand">@{{page.page_type_name}}</button>
					</div>
				</div>
			</div>
			<div class="m-portlet__body">
				<div class="m-widget19">
					<div class="m-widget19__pic m-portlet-fit--top m-portlet-fit--sides"
						style="min-height-: 286px">

						<img :src="'/' + page.imagen_file.file_front_path"
						alt="">
						<h3 class="m-widget19__title m--font-light truncate">
							@{{page.title}}
						</h3>
						<div class="m-widget19__shadow"></div>
					</div>
					<div class="m-widget19__content">
						<div class="m-widget19__header">
							<div class="m-widget19__user-img">
								<img class="m-widget19__img" :src="'/public/img/profile/' + page.user.username + '/' + page.user.user_data.avatar" alt="">
							</div>
							<div class="m-widget19__info">
								<span class="m-widget19__username">
								@{{page.user.user_data.nombre + ' ' +page.user.user_data.apellido}}
								</span><br>
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
						<div class="m-widget19__body"  v-html="contentText"></div>
					</div>
					<div class="m-widget19__action">
						<a :href="getPageFullPath(page.path)" class="btn m-btn--pill btn-secondary m-btn m-btn--hover-brand m-btn--custom">
							<span v-if="page.status == 1">
								View
								</span>
								<span v-else>
								Edit
								</span>	
							</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>