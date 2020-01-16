@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <form action="<?php echo $action; ?>" method="post" id="form" class="col s12 m10 l10">
            <input type="hidden" name="id_form" value="<?php echo element('id', $pagina, ''); ?>">
            <span class="header grey-text text-darken-2">Datos básicos <i class="material-icons left">description</i></span>
            <div class="input-field">
                <label for="path">Path:</label>
                <input type="text" id="path" name="path" required="required" value="<?php echo element('path', $pagina, ''); ?>">
            </div>
            <div class="input-field">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required="required" value="<?php echo element('title', $pagina, ''); ?>">
            </div>
            <div id="introduction" class="section scrollspy">
                <label for="id_cazary">Contenido:</label>
                <div class="input-field">
                    <textarea id="id_cazary" name="content"><?php echo element('content', $pagina, ''); ?></textarea>
                </div>
                <br>
            </div>
            <?php if (5 > $ci->session->userdata('level')) : ?>
                <br>
                Publicar categoria
                <br>
                <div class="input-field">
                    <div class="switch">
                        <label>
                            No publicado
                            <input type="checkbox" name="status_form" value="1" <?php if (element('status', $pagina, '') == "1") : ?> checked="checked" <?php endif ?>>
                            <span class="lever"></span>
                            Publicado
                        </label>
                    </div>
                </div>
            <?php endif ?>
            <br><br>
            <div class="input-field" id="buttons">
                <a href="<?php echo base_url('admin/paginas/'); ?>" class="btn red darken-1">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="material-icons right">done</i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection