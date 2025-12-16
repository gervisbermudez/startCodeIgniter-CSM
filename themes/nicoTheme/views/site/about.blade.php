@extends('site.layouts.site')

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
<!-- Page Content -->
  <div class="container">
    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">{{$page->title}}
      <small>{{$page->subtitle}}</small>
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{base_url()}}">Home</a>
      </li>
      <li class="breadcrumb-item active">{{$page->title}}</li>
    </ol>
    <!-- Intro Content -->
    <?= $page->content ?>
  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
