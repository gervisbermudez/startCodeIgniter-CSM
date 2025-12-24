@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
	<div class="row">
		<div class="col s12 m7 l8" id="<?php echo $itemid; ?>">
			<div class="card event">
				
				<div class="card-image">
					<?php
					$rawPreview = isset($video['preview']) ? trim($video['preview']) : '';
					$rawPreview = str_replace(['?>', "?&gt;"], '', $rawPreview);
					if ($rawPreview && (strpos($rawPreview, 'http://') === 0 || strpos($rawPreview, 'https://') === 0)) {
						$imgSrc = $rawPreview;
					} elseif ($rawPreview && strpos($rawPreview, '/') === 0) {
						$imgSrc = base_url(ltrim($rawPreview, '/'));
					} elseif ($rawPreview) {
						$imgSrc = base_url($rawPreview);
					} else {
						$imgSrc = base_url('public/img/default.jpg');
					}
					?>
					<img src="<?php echo $imgSrc; ?>" alt="" class="materialboxed">
				</div>
				<div class="card-content">
					<p><?php echo element('description', $video, ''); ?> <br></p>
				</div>
				<div class="card-action">
					
					<a href="#!" data-activates='options' class="dropdown-button right"><i
							class="material-icons">more_vert</i></a>
					<?php echo $options; ?>
				</div>
			</div>
		</div>
		<div class="col s12 m5 l4">
			<div class="card">
				<div class="card-content">
					<p style="margin-bottom:0.5em;"><b><?= lang('videos_status') ?>:</b> 
						<?php echo (element('status', $video, '') == '1') ? lang('videos_published') : lang('videos_draft'); ?>
					</p>
					<p style="margin-bottom:0.5em;"><b><?= lang('videos_publish_date') ?>:</b> <?php echo element('date_publish', $video, element('fecha', $video, '')); ?></p>
					<?php if (element('nam', $video, '') && element('nam', $video, '') !== element('nombre', $video, '')): ?>
						<p style="margin-bottom:0.5em;"><b><?= lang('videos_name') ?> (ALT):</b> <?php echo element('nam', $video, ''); ?></p>
					<?php endif; ?>
					<?php if (isset($video['user']) && is_array($video['user'])): ?>
						<p style="margin-bottom:0.5em;"><b><?= lang('videos_author') ?>:</b> <?php echo element('nombre', $video['user'], '') . ' ' . element('apellido', $video['user'], ''); ?></p>
					<?php endif; ?>
					<p><?php echo element('fecha', $video, ''); ?></p>
				</div>
				<div class="card-action">
					   <?= lang('video_duration') ?>: <?php echo element('duration', $video, ''); ?> <br>
					   <?= lang('video_payinfo') ?>: <?php echo element('payinfo', $video, ''); ?> <br>
					   <?= lang('video_youtube_url') ?>: <?php echo element('youtubeid', $video, ''); ?> <br>
					<?php if ($categorias): ?>
					   <b><?= lang('video_categories') ?>:</b>
					<ul>
						<?php foreach ($categorias as $key => $value): ?>
						<li><a href="#"><?php echo element('categoria', $value, ''); ?></a></li>
						<?php endforeach ?>
					</ul>
					<?php endif ?>
				</div>
			</div>

			<?php if (5 > $this->session->userdata('level')): ?>
					<div>
						<div class="switch left">
						<label>
							   <?= lang('video_not_published') ?>
							<input type="checkbox" class="change_state" name="status"
								<?php if ($video['status']=="1"): ?> checked="checked"
								data-url="admin/videos/fn_ajax_change_state/"
								data-action-param='{"id":"<?php echo $video['id']; ?>", "table":"video"}'
								<?php endif ?>>
								<span class="lever"></span>
							   <?= lang('video_published') ?>
						</label>
					</div>
					</div>
						<div>
							<a href="<?= base_url('admin/videos/editar/' . element('id', $video, element('video_id', $video, ''))) ?>" class="waves-effect waves-light btn">
							<i class="material-icons left">edit</i><?= lang('edit') ?>
						</a>
						</div>
					<?php endif ?>
		</div>
	</div>
</div>
</div>
</div>
<div id="<?php echo $modalid; ?>" class="modal">
	<div class="modal-content">
		<h4><?= lang('video_delete_title') ?></h4>
		<p><?= lang('video_delete_confirm') ?></p>
	</div>
	<div class="modal-footer">
		<a href="#" data-action="acept" class=" modal-action modal-close waves-effect waves-green btn-flat"><?= lang('accept') ?></a>
		<a href="#" class=" modal-action modal-close waves-effect waves-green btn-flat"><?= lang('cancel') ?></a>
	</div>
</div>
@endsection