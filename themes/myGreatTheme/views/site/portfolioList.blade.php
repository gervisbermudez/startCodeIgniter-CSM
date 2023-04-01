@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
  <!-- Page Content -->
  <div class="container">

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Portfolio 3
      <small>Subheading</small>
    </h1>

    <div class="section">
    <nav class="breadcrumbs">
      <div class="nav-wrapper">
        <div class="col s12">
          <a href="{{base_url()}}" class="breadcrumb">Home</a>
          <a href="#!" class="breadcrumb">Portfolio </a>
        </div>
      </div>
    </nav>
  </div>

    <div class="row">
      <div class="col l4 col-sm-6 portfolio-item">
        <div class="card h-100">
          <div class="card-image">
            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
          </div>
          <div class="card-body card-content">
            <h4 class="card-title">
              <a href="#">Project One</a>
            </h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet numquam aspernatur eum quasi sapiente nesciunt? Voluptatibus sit, repellat sequi itaque deserunt, dolores in, nesciunt, illum tempora ex quae? Nihil, dolorem!</p>
          </div>
        </div>
      </div>
      <div class="col l4 col-sm-6 portfolio-item">
        <div class="card h-100">
          <div class="card-image">
            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
          </div>
          <div class="card-body card-content">
            <h4 class="card-title">
              <a href="#">Project Two</a>
            </h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
          </div>
        </div>
      </div>
      <div class="col l4 col-sm-6 portfolio-item">
        <div class="card h-100">
          <div class="card-image">
            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
          </div>
          <div class="card-body card-content">
            <h4 class="card-title">
              <a href="#">Project Three</a>
            </h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos quisquam, error quod sed cumque, odio distinctio velit nostrum temporibus necessitatibus et facere atque iure perspiciatis mollitia recusandae vero vel quam!</p>
          </div>
        </div>
      </div>
      <div class="col l4 col-sm-6 portfolio-item">
        <div class="card h-100">
          <div class="card-image">
            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
          </div>
          <div class="card-body card-content">
            <h4 class="card-title">
              <a href="#">Project Four</a>
            </h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
          </div>
        </div>
      </div>
      <div class="col l4 col-sm-6 portfolio-item">
        <div class="card h-100">
          <div class="card-image">
            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
          </div>
          <div class="card-body card-content">
            <h4 class="card-title">
              <a href="#">Project Five</a>
            </h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>
          </div>
        </div>
      </div>
      <div class="col l4 col-sm-6 portfolio-item">
        <div class="card h-100">
          <div class="card-image">
            <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>
          </div>
          <div class="card-body card-content">
            <h4 class="card-title">
              <a href="#">Project Six</a>
            </h4>
            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Itaque earum nostrum suscipit ducimus nihil provident, perferendis rem illo, voluptate atque, sit eius in voluptates, nemo repellat fugiat excepturi! Nemo, esse.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <ul class="pagination">
      <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
      <li class="active"><a href="#!">1</a></li>
      <li class="waves-effect"><a href="#!">2</a></li>
      <li class="waves-effect"><a href="#!">3</a></li>
      <li class="waves-effect"><a href="#!">4</a></li>
      <li class="waves-effect"><a href="#!">5</a></li>
      <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
    </ul>

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
