@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
@include('site.shared.navbar')
@endsection

@section('content')
<!-- Page Content -->
<div class="container">

  <div class="section">
    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Services
      <small>Subheading</small>
    </h1>
  </div>


  <div class="section">
    <nav class="breadcrumbs">
      <div class="nav-wrapper">
        <div class="col s12">
          <a href="{{base_url()}}" class="breadcrumb">Home</a>
          <a href="#!" class="breadcrumb">Services</a>
        </div>
      </div>
    </nav>
  </div>

  <!-- Image Header -->
  <img class="responsive-img rounded mb-4" src="http://placehold.it/1200x300" alt="">

  <!-- Marketing Icons Section -->
  <div class="row">
    <div class="col l4 mb-4">
      <div class="card h-100">
        <div class="card-content">
          <h4 class="card-title">Card Title</h4>
          <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus
            neque.</p>
        </div>
        <div class="card-action">
          <a href="#" class="btn btn-primary">Learn More</a>
        </div>
      </div>
    </div>
    <div class="col l4 mb-4">
      <div class="card h-100">
        <div class="card-content">
          <h4 class="card-title">Card Title</h4>
          <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus
            neque.</p>
        </div>
        <div class="card-action">
          <a href="#" class="btn btn-primary">Learn More</a>
        </div>
      </div>
    </div>
    <div class="col l4 mb-4">
      <div class="card h-100">
        <div class="card-content">
          <h4 class="card-title">Card Title</h4>
          <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus
            neque.</p>
        </div>
        <div class="card-action">
          <a href="#" class="btn btn-primary">Learn More</a>
        </div>
      </div>
    </div>
  </div>
  <!-- /.row -->

</div>
<!-- /.container -->
@endsection

@section('footer')
@include('site.shared.footer')
@endsection