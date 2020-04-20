@extends('admin.layouts.app')

@section('title', $title)

@section('header')
  <nav class="page-navbar">
    <div class="nav-wrapper">
		<form>
			<div class="input-field">
			<input id="search" type="search" required>
			<label class="label-icon" for="search"><i class="material-icons">search</i></label>
			<i class="material-icons">close</i>
			</div>
      	</form>
      <ul class="right hide-on-med-and-down">
        <li><a href="badges.html"><i class="material-icons">view_module</i></a></li>
        <li><a href="collapsible.html"><i class="material-icons">refresh</i></a></li>
        <li><a href="mobile.html"><i class="material-icons">more_vert</i></a></li>
      </ul>
    </div>
  </nav>
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col s12">
			@if ($paginas && count($paginas) > 0)
			<table>
				<thead>
					<tr>
						<th>Page Title</th>
						<th>Path</th>
						<th>Author</th>
						<th>Publish Date</th>
						<th>Status</th>
						<th>Visibility</th>
						<th>Options</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($paginas as $page)
					<tr>
						<td>{{$page->title}}</td>
						<td><a href="{{base_url($page->path)}}" target="_blank">{{$page->path}}</a></td>
						<td><a href="{{base_url('admin/usuarios/ver/').$page->author}}">{{$page->username}}</a></td>
						<td>
							{{$page->date_publish ? $page->date_publish : $page->date_create}}
						</td>
						<td>
						@if ($page->status == 1)
							<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publicado" >publish</i>
						@else
							<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Borrador" >edit</i>
						@endif
						</td>
						<td>
						@if ($page->visibility == 1)
							<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Publico" >public</i>
						@else
							<i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Privado" >lock</i>
						@endif
						</td>
						<td>
							<a class='dropdown-trigger' href='#' data-target='dropdown{{$page->page_id}}'><i class="material-icons">more_vert</i></a>
							<ul id='dropdown{{$page->page_id}}' class='dropdown-content'>
								<li><a href="{{base_url()}}admin/paginas/editar/{{$page->page_id}}">Editar</a></li>
								<li><a href="{{base_url()}}admin/paginas/editar/{{$page->page_id}}">Borrar</a></li>
								<li><a href="{{base_url($page->path)}}" target="_blank">Preview</a></li>
								<li><a href="{{base_url($page->path)}}" target="_blank">Archivar</a></li>
							</ul>
						</td>
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