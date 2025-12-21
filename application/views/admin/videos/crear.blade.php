@extends('admin.layouts.app')
@section('title', $title)
@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>" />
@endsection
@section('content')
<div class="container form">
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{ $h1 }}</h3>
        </div>
    </div>
    <div class="row">
        <form action="<?php echo base_url($action); ?>" method="post" id="form" class="col s12 m10 l10">
            <input type="hidden" name="id" value="<?php echo element('id', $video, ''); ?>">
                <span class="header grey-text text-darken-2"><?= lang('videos_basic_data') ?> <i
                    class="material-icons left">description</i></span>
            <div class="input-field">
                <label for="nombre"><?= lang('videos_name') ?>:</label>
                <input type="text" id="nombre" name="nombre" required="required"
                    value="<?php echo element('nombre', $video, ''); ?>">
            </div>
            <div id="introduction" class="section scrollspy">
                <label for="id_cazary"><?= lang('videos_description') ?>:</label>
                <div class="input-field">
                    <textarea id="id_cazary" name="description">
							<?php echo element('description', $video, ''); ?>
						</textarea>
                </div>
                <br>
            </div>
            <div class="input-field">
                <label for="duracion"><?= lang('videos_duration') ?>:</label>
                <input type="text" id="duracion" name="duration" required="required"
                    value="<?php echo element('duration', $video, ''); ?>">
            </div>
            <div class="input-field">
                <label for="youtubeid"><?= lang('videos_youtube_url') ?>:</label>
                <input type="text" id="youtubeid" name="youtubeid" required="required"
                    value="<?php echo element('youtubeid', $video, ''); ?>">
            </div>
            <div class="input-field">
                <label for="paypal"><?= lang('videos_payment_info') ?>:</label>
                <input type="text" id="paypal" name="paypal" required="required"
                    value="<?php echo element('payinfo', $video, ''); ?>">
            </div>
                <span id="preview" class="header grey-text text-darken-2 scrollspy"><?= lang('videos_preview') ?> <i
                    class="material-icons left">perm_media</i></span>
            <div class="input-field">
                <input type="text" id="imagen" class="modal-trigger" name="preview" placeholder="<?= lang('videos_main_image') ?>"
                    data-expected="inactive" data-target="modal1" value="<?php echo element('preview', $video, ''); ?>">
            </div>
            <br>
            <div class="input-field">
                <select name="categorias[]" multiple>
                    <option value="" disabled selected><?= lang('videos_select') ?></option>
                    <?php if ($categorias): ?>
                    <?php foreach ($categorias as $key => $categoria): ?>
                    <option value="<?php echo $categoria['id'] ?>"
                        <?php if (array_key_exists($categoria['id'], $videocategoria)): ?> selected <?php endif?>>
                        <?php echo $categoria['nombre'] ?></option>
                    <?php endforeach?>
                    <?php endif?>
                </select>
                <label><?= lang('videos_categories') ?>:</label>
            </div>
            <?php if (5 > $ci->session->userdata('level')): ?>
            <!-- Switch -->
            <br>
            <?= lang('videos_publish_video') ?>
            <br>
            <div class="input-field">
                <div class="switch">
                    <label>
                        <?= lang('videos_not_published') ?>
                        <input type="checkbox" name="status" value="1"
                            <?php if (element('status', $video, '') == "1"): ?> checked="checked" <?php endif?>>
                        <span class="lever"></span>
                        <?= lang('videos_published') ?>
                    </label>
                </div>
            </div>
            <?php endif?>
            <br><br>
            <div class="input-field" id="buttons">
                <a href="<?php echo base_url('admin/videos/'); ?>" class="btn-flat"><?= lang('videos_cancel') ?></a>
                <button type="submit" class="btn btn-primary"><i class="material-icons right">done</i> <?= lang('videos_save') ?></button>

            </div>
        </form>
        <div class="col hide-on-small-only m2 l2">
            <ul class="section table-of-contents tabs-wrapper">
                <li><a href="#introduction"><?= lang('videos_info') ?></a></li>
                <li><a href="#preview"><?= lang('videos_image') ?></a></li>
            </ul>
        </div>
    </div>
</div>
<div id="modal1" class="modal bottom-sheet" data-navigatefiles="active">
    <div class="modal-content">
        <h4><?= lang('videos_select_image') ?></h4>
        <div class="row gallery" data-active-dir="./img">
        </div>
    </div>
</div>
@endsection