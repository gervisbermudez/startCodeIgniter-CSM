@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="container large">
<h3>Bienvenido {{$ci->session->userdata('username')}}</h3>
</div>
<div class="fixed-action-btn">
	<a data-position="left"
		data-delay="50" data-tooltip="Formulario nuevo" class="btn-floating btn-large tooltipped red" href="{{base_url('admin/formularios/new')}}">
		<i class="large material-icons">add</i>
	</a>
	<ul>
		<li><a data-position="left"
		data-delay="50" data-tooltip="Usuario nuevo" class="btn-floating tooltipped red" href="{{base_url('admin/user/agregar')}}"><i class="material-icons">perm_identity</i></a></li>
		<li><a data-position="left"
		data-delay="50" data-tooltip="Pagina nueva" class="btn-floating tooltipped yellow darken-1" href="{{base_url('admin/paginas/nueva/')}}"><i class="material-icons">web</i></a></li>
		<li><a data-position="left"
		data-delay="50" data-tooltip="Album nuevo" class="btn-floating tooltipped green" href="{{base_url('admin/galeria/crearalbum/')}}"><i class="material-icons">publish</i></a></li>
		<li><a data-position="left"
		data-delay="50" data-tooltip="Evento nuevo" class="btn-floating tooltipped blue" href="{{ base_url('admin/eventos/agregar/') }}"><i class="material-icons">assistant</i></a></li>
	</ul>
</div>
@endsection