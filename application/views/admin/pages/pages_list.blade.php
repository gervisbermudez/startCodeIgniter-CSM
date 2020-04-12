@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col s12">
			@if ($paginas && count($paginas) > 0)
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Item Name</th>
						<th>Item Price</th>
						<th>Editar</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($paginas as $page)
					<tr>
						<td>{{$page->title}}</td>
						<td><a href="{{base_url($page->path)}}" target="_blank">{{$page->path}}</a></td>
						<td>{{$page->author}}</td>
						<td><a href="{{base_url()}}admin/paginas/editar/{{$page->page_id}}">Editar</a></td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endif
		</div>
	</div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
	<a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
		data-delay="50" data-tooltip="Formulario nuevo" href="{{base_url('admin/paginas/nueva/')}}">
		<i class="large material-icons">add</i>
	</a>
</div>
@endsection