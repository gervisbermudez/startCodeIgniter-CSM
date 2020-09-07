@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection

@section('content')
<div class="container form">
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{$h1}}</h3>
        </div>
    </div>
    <div class="row">
        <div class="col s12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    Crear Album
                </div>
                <div class="panel-body">
                    <?php echo form_open_multipart('Admin/Galeria/savealbum/');?>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="first_name2" type="text" name="nombre" class="validate">
                            <label for="first_name2">Nombre del Album</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="id_cazary" class="capitalize">Descripcion del Album:</label>
                        <textarea class="form-control" id="id_cazary" placeholder="Descripcion del Album" name="descripcion" value=""></textarea>
                    </div>
                    <br><br>
                    <label for="publishdate" class="capitalize">Fecha del album:</label>
                    <div class="form-group input-group">
                        <input type="text" class="datepicker" placeholder="Fecha" name="fecha" required="required">
                    </div>
                    <br><br>
                    <div class="col s12">
                        Publicar album
                        <!-- Switch -->
                        <div class="switch">
                            <label>
                                No publicado
                                <input type="checkbox" name="estatus">
                                <span class="lever"></span>
                                Publicado
                            </label>
                        </div>
                    </div>
                    <div class="col s12">
                        <br><br>
                        <a href="<?php echo base_url().'index.php/Admin/Galeria/' ?>" class="btn btn-flat">Cancelar</a>
                        <button type="submit" class="btn"><i class="material-icons right">done</i>Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
