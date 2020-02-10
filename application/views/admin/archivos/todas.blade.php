@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
	<div class="row">
		<?php
			$directorio = scandir($dir);
			$arraydir = explode('/',$dir);
			$quotes = array(" indigo", "blue"," cyan",  "green", "blue-grey", "lime", 'grey');
			$deep = array("darken-1", 'accent-4', '');
			$modalid = random_string('alnum', 16);
		?>
		<input type="hidden" value="<?php echo $dir ?>" name="dir" id="dir">
		<div class="col s12">
			<div class="nav-wrapper blue">
				<?php $ant = ''; foreach ($arraydir as $key => $value): ?>
				<a href="<?php echo base_url('index.php/Admin/Archivos/index/'.$ant.''.$value) ?>"
					class="breadcrumb"><?php if ($value==='.'): ?>
					Home
					<?php else: ?>
					<?php echo $value?>
					<?php endif ?>
				</a>
				<?php 
							$ant .= $value.'_'; 
						?>
				<?php endforeach ?>
				<?php if ($dir=='./trash'): ?>
				<a href="#<?php echo $modalid;?>" class="modal-trigger right white-text"
					data-action-param='{"folder":"./trash"}' data-url="admin/archivos/fn_empty_folder/"
					data-ajax-action="inactive" data-target-selector=".row.gallery">Vaciar Papelera</a>
				<?php endif ?>
			</div>
			<br>
		</div>
		<div class="col s12">
			<div class="row gallery">
				<?php $i = 0; ?>
				<?php 	foreach ($directorio as $key => $value): ?>
				<?php 
					$itemid = random_string('alnum', 16);
					$ddmid = random_string('alnum', 16);
				?>
				<?php if (strstr($value, '.gif') OR strstr($value, '.jpg') OR strstr($value, '.png')): ?>

				<div class="col m3 s4" id="<?php echo $itemid ?>">
					<div class="card">
						<a class="card-image" href="<?php echo base_url($dir.'/'.$value) ?>" data-lightbox="img">
							<img src="<?php echo base_url($dir.'/'.$value) ?>">
						</a>
						<div class="card-content">
							<span class="grey-text text-darken-4 truncate"><?php echo $value; ?></span>
							<!-- Dropdown Trigger -->
							<?php if (3 > $this->session->userdata('level')): ?>
							<a class='dropdown-button right' href='#' data-activates='<?php echo $ddmid ?>'>
								<i class="material-icons">more_vert</i></a>
							<!-- Dropdown Structure -->
							<ul id='<?php echo $ddmid ?>' class='dropdown-content'>
								<li>
									<a href="#<?php echo $modalid;?>" class="modal-trigger"
										data-action-param='{"file":"<?php echo $value; ?>","dir" : "<?php echo $dir ?>"}'
										data-url="admin/archivos/ajaxDeleteFile/" data-ajax-action="inactive"
										data-target-selector="<?php echo "#$itemid"; ?>">Eliminar</a>
								</li>
							</ul>
							<?php endif ?>
						</div>
					</div>
				</div>
				<?php else: ?>
				<?php if ($value!='..' && $value!='.'): ?>
				<?php
						$pos = strpos($value, '.');
						 if ($pos !== false): ?>
				<div class="col m3 s4" id="<?php echo $itemid ?>">
					<div class="card">
						<a class="card-content <?php echo random_element($quotes); ?> <?php echo random_element($deep); ?>"
							href="<?php  echo base_url('/'.str_replace('_','/',$dir).'/'.$value) ?>">
							<div class="center-align white-text"><i class="material-icons small ">description</i></div>
							<div class="center-align white-text"><?php echo $value ?></div>
						</a>
						<div class="card-content">
							<?php echo $value ?>
							<?php if (false): ?>
							<a class='dropdown-button right' href='#' data-activates='<?php echo $ddmid ?>'><i
									class="material-icons">more_vert</i></a>
							<!-- Dropdown Structure -->
							<ul id='<?php echo $ddmid ?>' class='dropdown-content'>
								<li><a href="#<?php echo $modalid;?>" class="modal-trigger"
										data-action-param='{"file":"<?php echo $value; ?>","dir" : "<?php echo $dir ?>"}'
										data-url="admin/archivos/ajaxDeleteFile/" data-ajax-action="inactive"
										data-target-selector="<?php echo "#$itemid"; ?>">Eliminar</a></li>
							</ul>
							<?php endif ?>
						</div>
					</div>
				</div>
				<?php else: ?>
				<div class="col m3 s4">
					<div class="card">
						<a class="card-content <?php echo random_element($quotes); ?> <?php echo random_element($deep); ?>"
							href="<?php echo base_url('index.php/Admin/Archivos/index/'.$udir.'_'.$value) ?>">
							<div class="center-align white-text"><i
									class="material-icons small "><?php if ($value=='trash'): ?>
									delete
									<?php else: ?>folder
									<?php endif ?>
								</i></div>
							<div class="center-align white-text"><?php  echo $value ?></div>
						</a>
						<div class="card-content">
							<?php echo $value ?>
						</div>
					</div>
				</div>
				<?php endif ?>
				<?php endif ?>
				<?php endif ?>
				<?php endforeach ?>
			</div>
		</div>
	</div>
</div>
</div>
<div id="<?php echo $modalid; ?>" class="modal">
	<div class="modal-content">
		<h4>Eliminar archivo(s)</h4>
		<p>¿Desea continuar? Esta accion no podrá deshacerse</p>
	</div>
	<div class="modal-footer">
		<a href="#!" data-action="acept" class=" modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div> <!-- Modal Trigger -->
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
	<a class="btn-floating btn-large red waves-effect waves-circle waves-light  modal-trigger tooltipped"
		data-position="left" data-delay="50" data-tooltip="Subir Archivo" href="#modal1"><i
			class="material-icons">publish</i>
	</a>
</div>
<!-- Modal Structure -->
<div id="modal1" class="modal bottom-sheet">
	<div class="modal-content">
		<h4>Subir Imagen</h4>
		<?php echo $form ?>
	</div>
	<div class="modal-footer">
		<a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Cancelar</a>
	</div>
</div>
@endsection