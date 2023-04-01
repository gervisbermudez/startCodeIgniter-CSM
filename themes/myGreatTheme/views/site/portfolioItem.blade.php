@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
  <!-- Page Content -->
  <div class="container">

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Portfolio Item
      <small>Subheading</small>
    </h1>

    <div class="section">
    <nav class="breadcrumbs">
      <div class="nav-wrapper">
        <div class="col s12">
          <a href="{{base_url()}}" class="breadcrumb">Home</a>
          <a href="#!" class="breadcrumb">Portfolio Item</a>
        </div>
      </div>
    </nav>
  </div>

    <!-- Portfolio Item Row -->
    <div class="row">

      <div class="col m8">
        <img class="responsive-img" src="http://placehold.it/750x500" alt="">
      </div>

      <div class="col m4">
        <h3 class="my-3">Project Description</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum nec metus. Mauris ultricies, justo eu convallis placerat, felis enim.</p>
        <h3 class="my-3">Project Details</h3>
        <ul>
          <li>Lorem Ipsum</li>
          <li>Dolor Sit Amet</li>
          <li>Consectetur</li>
          <li>Adipiscing Elit</li>
        </ul>
      </div>

    </div>
    <!-- /.row -->

    <!-- Related Projects Row -->
    <h3 class="my-4">Related Projects</h3>

    <div class="row">

      <div class="col m3 s6 ">
        <a href="#">
          <img class="responsive-img" src="http://placehold.it/500x300" alt="">
        </a>
      </div>

      <div class="col m3 s6 ">
        <a href="#">
          <img class="responsive-img" src="http://placehold.it/500x300" alt="">
        </a>
      </div>

      <div class="col m3 s6 ">
        <a href="#">
          <img class="responsive-img" src="http://placehold.it/500x300" alt="">
        </a>
      </div>

      <div class="col m3 s6 ">
        <a href="#">
          <img class="responsive-img" src="http://placehold.it/500x300" alt="">
        </a>
      </div>

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
