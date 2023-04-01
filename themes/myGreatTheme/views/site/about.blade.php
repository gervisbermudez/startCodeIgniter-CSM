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
      <h1 class="mt-4 mb-3">About
        <small>Subheading</small>
      </h1>
    </div>


    <div class="section">
      <nav class="breadcrumbs">
        <div class="nav-wrapper">
          <div class="col s12">
            <a href="{{base_url()}}" class="breadcrumb">Home</a>
            <a href="#!" class="breadcrumb">About</a>
          </div>
        </div>
      </nav>
    </div>

    <!-- Intro Content -->
    <div class="row">
      <div class="col l6">
        <img class="responsive-img rounded mb-4 responsive-img" src="http://placehold.it/750x450" alt="">
      </div>
      <div class="col l6">
        <h2>About Modern Business</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sed voluptate nihil eum consectetur similique? Consectetur, quod, incidunt, harum nisi dolores delectus reprehenderit voluptatem perferendis dicta dolorem non blanditiis ex fugiat.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe, magni, aperiam vitae illum voluptatum aut sequi impedit non velit ab ea pariatur sint quidem corporis eveniet. Odit, temporibus reprehenderit dolorum!</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Et, consequuntur, modi mollitia corporis ipsa voluptate corrupti eum ratione ex ea praesentium quibusdam? Aut, in eum facere corrupti necessitatibus perspiciatis quis?</p>
      </div>
    </div>
    <!-- /.row -->

    <div class="section">
      <!-- Team Members -->
      <h2>Our Team</h2>

      <div class="row">
        <div class="col l4 mb-4">
          <div class="card h-100 text-center">
            <div class="card-image">
              <img class="card-img-top" src="http://placehold.it/750x450" alt="">
            </div>
            <div class="card-content">
              <h4 class="card-title">Team Member</h4>
              <h6 class="card-subtitle mb-2 text-muted">Position</h6>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>
            </div>
            <div class="card-action">
              <a href="#">name@example.com</a>
            </div>
          </div>
        </div>
        <div class="col l4 mb-4">
          <div class="card h-100 text-center">
            <div class="card-image">
              <img class="card-img-top" src="http://placehold.it/750x450" alt="">
            </div>
            <div class="card-content">
              <h4 class="card-title">Team Member</h4>
              <h6 class="card-subtitle mb-2 text-muted">Position</h6>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>
            </div>
            <div class="card-action">
              <a href="#">name@example.com</a>
            </div>
          </div>
        </div>
        <div class="col l4 mb-4">
          <div class="card h-100 text-center">
            <div class="card-image">
              <img class="card-img-top" src="http://placehold.it/750x450" alt="">
            </div>
            <div class="card-content">
              <h4 class="card-title">Team Member</h4>
              <h6 class="card-subtitle mb-2 text-muted">Position</h6>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>
            </div>
            <div class="card-action">
              <a href="#">name@example.com</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.row -->

    <!-- Our Customers -->
    <h2>Our Customers</h2>
    <div class="row">
      <div class="col l2 col-sm-4 mb-4">
        <img class="responsive-img" src="http://placehold.it/500x300" alt="">
      </div>
      <div class="col l2 col-sm-4 mb-4">
        <img class="responsive-img" src="http://placehold.it/500x300" alt="">
      </div>
      <div class="col l2 col-sm-4 mb-4">
        <img class="responsive-img" src="http://placehold.it/500x300" alt="">
      </div>
      <div class="col l2 col-sm-4 mb-4">
        <img class="responsive-img" src="http://placehold.it/500x300" alt="">
      </div>
      <div class="col l2 col-sm-4 mb-4">
        <img class="responsive-img" src="http://placehold.it/500x300" alt="">
      </div>
      <div class="col l2 col-sm-4 mb-4">
        <img class="responsive-img" src="http://placehold.it/500x300" alt="">
      </div>
    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
