<?php 
	$this->load->helper('array');
	$this->load->helper('text');
	$quotes = array(" indigo", "blue"," cyan",  "green", "pink", "lime", 'orange');
	$deep = array("darken-1", 'accent-4', ''); 
?>
<?php if ($mensajes): ?>
<?php foreach ($mensajes as $array): ?>
<a class="collection-item avatar message" href="ver/<?php echo $array['id'] ?>" data-id="<?php echo $array['id'] ?>" >
	<i class="circle  <?php echo random_element($quotes); ?> <?php echo random_element($deep); ?>">
		<?php echo substr($array['_from'],0,1); ?></i>
	<span class="title"><?php echo $array['_from'] ?></span>
	
	<?php echo character_limiter($array['_mensaje'],25); ?>
	<?php if ($array['estatus']=='1'): ?>
	<?php else: ?>
	<span class="new badge">1</span>
	<?php endif ?>
	<span class="date"><?php
				$fecha = DateTime::createFromFormat('Y-m-d H:i:s',$array['fecha']);
				echo $fecha->format('H:i');
	?></span>
</a>
<?php endforeach ?>
<?php endif ?>