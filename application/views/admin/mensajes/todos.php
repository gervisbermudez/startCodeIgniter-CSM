<div class="main">
	<?php
		$this->load->helper('text');
	?>
	<div class="container">
		<div class="row mensaje-container">
			<div class="col s12 search-bar">
				<nav class="red darken-1">
					<span class="current-folder"><?php echo $folder; ?></span>
					<span class="options"><i class="material-icons">more_vert</i></span>
					<div class="nav-wrapper">
						<form>
							<div class="input-field">
								<input id="search" type="search" required>
								<label for="search"><i class="material-icons">search</i></label>
								<i class="material-icons">close</i>
							</div>
						</form>
					</div>
					
				</nav>
			</div>
			<div class="col m1 s2 folders">
				<a href="#" class="tooltipped  folder" data-folder="1" data-position="right" data-delay="50" data-tooltip="Inbox"><i class="material-icons">inbox</i></a>
				<a href="#" class="tooltipped folder" data-folder="2" data-position="right" data-delay="50" data-tooltip="Archived"><i class="material-icons">archive</i></a>
				<a href="#" class="tooltipped folder" data-folder="3" data-position="right" data-delay="50" data-tooltip="Send"><i class="material-icons">send</i></a>
				<a href="#" class="tooltipped folder" data-folder="7" data-position="right" data-delay="50" data-tooltip="Drafts"><i class="material-icons">drafts</i></a>
				<a href="#" class="tooltipped folder" data-folder="6" data-position="right" data-delay="50" data-tooltip="Starred"><i class="material-icons">grade</i></a>
				<a href="#" class="tooltipped folder" data-folder="4" data-position="right" data-delay="50" data-tooltip="Deleted"><i class="material-icons">delete</i></a>
				<a href="#" class="tooltipped folder" data-folder="5" data-position="right" data-delay="50" data-tooltip="Spam"><i class="material-icons">report</i></a>
			</div>
			<div class="col m5 s10 l4" id="list-folder">
				<div class="collection" data-active-folder="Inbox">
					<?php echo $list ?>
				</div>
			</div>
			<div class="col s12 m6 l7 card" id="mail-conten">
				<div class="card-content" data-id="<?php echo $mensajes[0]['id'] ?>">
					<div class="preview">
						<?php if (isset($preview)): ?>
						<?php echo $preview ?>
						<?php endif ?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<div class="fixed-action-btn" style="bottom: 45px; right: 24px;"><a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Nuevo Mensaje"  href="#" id="mail-create"><i class="large material-icons">add</i></a></div>
	<div class="compose-container">
		<div class="compose-msg-cont">
			<div class="compose-msg-head blue darken-1 white-text">
				<span class="window-title">Nuevo Mensaje</span>
				<a href="#" class="white-text right close-compose-msg"><i class="material-icons">close</i></a>
				<a href="#" class="white-text right toggle-compose-msg"><i class="material-icons">expand_more</i></a>
			</div>
			<div class="compose-msg-body">
				<div class="chips"></div>
				<input id="subject" type="text" class="validate" placeholder="Subject">
				<textarea id="mensaje" class="materialize-textarea" placeholder="Mensaje"></textarea>
				<button class="btn waves-effect waves-light" type="submit" name="action">Enviar
				<i class="material-icons right">send</i>
				</button>
			</div>
		</div>
	</div>
	
</div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		fnSetMessageEvent();
		$('.folder').click(function(event) {
			fnToggleFormComposeMenssage('hide');
			var strIDFolder = $(this).attr('data-folder');
			$('.current-folder').html($(this).attr('data-tooltip'));
			fnGetFolder(strIDFolder);
		});
		$('#mail-create').click(function(event) {
			fnToggleFormComposeMenssage('show');
		});
		$('.compose-msg-head').click(function(event) {
			$(this).parents('.compose-msg-cont').toggleClass('close');
		});
		$('.close-compose-msg').click(function(event) {
			$(this).parents('.compose-msg-cont').hide('slow');
		});
		$('.chips').material_chip({
			'data': [],
			'placeholder': 'To:',
			'secondaryPlaceholder': '',
			'inputname' : '_to',
		});
	});
	var fnMoveMessageTo = function(strIDFolder, fnCallback){
		if ($('.collection').attr('data-active-folder') != strIDFolder) {
			var strIdMessage = $('#mensaje-id').attr('data-id');
			if (strIdMessage) {
				$.ajax({
					url: base_url+'admin/mensajes/update_messagefolder_byajax/',
					type: 'POST',
					dataType: 'json',
					data: {'id': strIdMessage,'folder':strIDFolder},
				})
				.done(function(json) {
					if (json.result) {
						$('.collection-item[data-id='+strIdMessage+']').hide('slow');
						$('.preview').html('');
						if (typeof(fnCallback)=='function') {
							fnCallback();
						};
					}else{
						Materialize.toast('Ocurrio un error', 4000);
					}
				})
				.fail(function() {
					console.log("fnMoveMessageTo error");
				})
				.always(function() {
					console.log("fnMoveMessageTo complete");
				});
			};
		};
	}
	var fnGetFolder = function (strIDFolder, fnCallback) {
		$.ajax({
			url: base_url+'admin/mensajes/getfolder/'+strIDFolder,
			type: 'POST',
			dataType: 'html'
		})
		.done(function(html) {
			$('.collection').html(html);
			$('.collection').attr('data-active-folder', strIDFolder);
			$('.preview').html('');

			fnSetMessageEvent();
			if (typeof(fnCallback)=='function') {
				fnCallback();
			};
		})
		.fail(function() {
			console.log("fnGetFolder error");
		})
		.always(function() {
			console.log("fnGetFolder complete");
		});
	}
	var fnSetMessageEvent = function() {
		$('.message').click(function(event) {
			var id_mensaje = $(this).attr('data-id');
			var element = this;
			$.ajax({
				url: base_url+'admin/mensajes/get_mensaje_by_ajax/',
				type: 'POST',
				dataType: 'html',
				data: {'id_mensaje': id_mensaje},
			})
			.done(function(html) {
				$('.preview').html(html);
				$(element).find('.new.badge').hide('slow');
				fnSetMessageOptionsEvent();
			})
			.fail(function() {
				console.log("fnSetMessageEvent error");
			})
			.always(function() {
				console.log("fnSetMessageEvent complete");
			});
		event.preventDefault();
		});
	}
	var fnSetMessageOptionsEvent = function(){
		$('.opt-reply').click(function(event) {
			$('.chip').remove();
			var strSubject = $('#_subject').html();
			var strTo = $('#from').html();
			fnToggleFormComposeMenssage('show');
			$(fnChipEmailTemplate(strTo)).insertBefore('#_to');
			$('#subject').val('FW: '+strSubject);
			
		});
		$('.opt-delete').click(function(event) {
			fnMoveMessageTo('4', function  () {
				Materialize.toast('Eliminado!', 8000);
			});
		});
		$('.opt-archive').click(function(event) {
			fnMoveMessageTo('2', function () {
				Materialize.toast('Archivado!', 8000);
			});
		});
		$('.opt-important').click(function(event) {
			fnMoveMessageTo('6', function () {
				Materialize.toast('Marcado como importante!', 8000);
			});
		});
	}
	var fnChipEmailTemplate = function(strEmail){
		return strChipTemplate = '<div class="chip"><span class="txt">'+strEmail+'</span><i class="close material-icons">close</i></div>';
	}
	var fnToggleFormComposeMenssage = function(toggle){
		if (toggle == 'show') {
			$('.chip').remove();
			$('.compose-msg-head').removeClass('close');
			$('.compose-msg-cont').show('slow');
		}else{
			$('.compose-msg-cont').hide();
		}
	var fnSetSaveDraftsEvents = function () {
		$('#mensaje').keyup(function(event) {
			console.log(event);
			var objFuntionsEvents = {
				//default: update or create draft
				'default' : function () {
					var strTargetUrl ='admin/mensajes/updatedraft/';
					if ('' == 'new') {
						strTargetUrl = 'admin/mensajes/setdraft/'
					};
					$.ajax({
						url: base_url+strTargetUrl,
						type: 'POST',
						dataType: 'json',
						data: {param1: 'value1'},
					})
					.done(function() {
						console.log("success");
					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
						console.log("complete");
					});
					
				}
			}
			if (objFuntionsEvents[event.keyCode]) {
				objFuntionsEvents[event.keyCode]();
			}else{
				objFuntionsEvents['default']();
			}
		});
	}
	}
</script>