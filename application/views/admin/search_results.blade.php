@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col s12">
            <h5>Pages</h5>
            @if($pages)
            <div class="row">
                @foreach($pages as $page)
                <div class="col s12 m4">
                    <div class="card">
                        <div class="card-image">
                            @if(isset($page->imagen_file))
                            <img src="{{base_url($page->imagen_file->file_front_path)}}">
                            @else
                            <img src="{{base_url('/public/img/default.jpg')}}">
                            @endif
                            <span class="card-title">{{$page->title}}</span>
                        </div>
                        <div class="card-content">
                            <p>{{character_limiter(strip_tags($page->content), 120)}}</p>
                        </div>
                        <div class="card-action">
                            <a href="{{base_url('admin/paginas/view/' . $page->page_id)}}">View</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@endsection