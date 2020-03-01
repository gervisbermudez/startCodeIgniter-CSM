@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col s12">
			@if ($forms_list)
			<table>
				<thead>
					<tr>
						<th>Form Name</th>
						<th>User</th>
						<th>date_create</th>
						<th>status</th>
						<th>Add Data</th>
						<th>Edit</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($forms_list as $form)
					<tr>
						<td>{{$form['form_name']}}</td>
						<td>{{$form['username']}}</td>
						<td>{{$form['date_create']}}</td>
						<td>{{$form['status']}}</td>
						<td><a href="{{base_url('admin/formularios/addData/' . $form['id'])}}"><i class="material-icons">add_to_photos</i></a></td>
						<td><a href="{{base_url('admin/formularios/editForm/' . $form['id'])}}"><i class="material-icons">edit</i></a></td>
						<td><a href="{{base_url('admin/formularios/deleteForm/' . $form['id'])}}"><i class="material-icons">delete</i></a></td>
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
		data-delay="50" data-tooltip="Nuevo Formulario" href="{{base_url('admin/formularios/new')}}">
		<i class="large material-icons">add</i>
	</a>
</div>
@endsection