<?php 
		$quotes = array(" indigo", "blue"," cyan",  "green", "pink", "lime", 'orange');
		$deep = array("darken-1", 'accent-4', '');
?>
<?php foreach ($mensajes as $key => $mensaje): ?>
	<div class="msg-prev" id="mensaje-id" data-id="<?php echo $mensaje['id']; ?>">
		<div class="msg-head">
			<span class="subject" id="_subject"><?php echo $mensaje['_subject']; ?></span>
			<div class="msg-options">
				<a href="#" class="msg-opt opt-reply"><i class="material-icons">reply</i></a>
				<a href="#" class="msg-opt opt-delete"><i class="material-icons">delete</i></a>
				<a href="#" class="msg-opt opt-archive"><i class="material-icons">archive</i></a>
				<a href="#" class="msg-opt opt-important"><i class="material-icons">grade</i></span>
				<a href="#" class="msg-opt opt-options"><i class="material-icons">more_vert</i></a>
			</div>
			<div class="separator"></div>
			<span class="from">
				<i class="circle <?php echo random_element($quotes).' '.random_element($deep); ?>"><?php echo substr($mensaje['_from'],0,1) ?></i>
				<span id="from"><?php echo $mensaje['_from']; ?></span>
			</span>
		</div>
		<div class="msg-body">
			<?php echo $mensaje['_mensaje'];?>
		</div>
		<div class="separator"></div>
	</div>
<?php endforeach ?>
