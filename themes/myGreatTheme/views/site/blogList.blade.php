@extends('site.layouts.site')

@section('title', $title)

@section('footer')
@include('site.shared.navbar')
@endsection

@section('content')
<!-- Page Content -->
<div class="container">

  <!-- Page Heading/Breadcrumbs -->
  <h1 class="mt-4 mb-3">Blog Home One
    <small>Subheading</small>
  </h1>

  <div class="section">
    <nav class="breadcrumbs">
      <div class="nav-wrapper">
        <div class="col s12">
          <a href="{{base_url()}}" class="breadcrumb">Home</a>
          <a href="#!" class="breadcrumb">Blog</a>
        </div>
      </div>
    </nav>
  </div>

  <div class="row">

    <!-- Blog Entries Column -->
    <div class="col m8">

      <!-- Blog Post -->
      <div class="card mb-4">
        <div class="card-image">
          <img class="card-img-top" src="http://placehold.it/750x300" alt="Card image cap">
        </div>
        <div class="card-body card-content">
          <h2 class="card-title">Post Title</h2>
          <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque,
            nulla? Quos cum ex quis soluta, a laboriosam. Dicta expedita corporis animi vero voluptate voluptatibus
            possimus, veniam magni quis!</p>
          <a href="#" class="btn btn-primary">Read More &rarr;</a>
        </div>
        <div class="card-footer card-action text-muted">
          Posted on January 1, 2017 by
          <a href="#">Start Bootstrap</a>
        </div>
      </div>

      <!-- Blog Post -->
      <div class="card mb-4">
        <div class="card-image">
          <img class="card-img-top" src="http://placehold.it/750x300" alt="Card image cap">
        </div>
        <div class="card-body card-content">
          <h2 class="card-title">Post Title</h2>
          <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque,
            nulla? Quos cum ex quis soluta, a laboriosam. Dicta expedita corporis animi vero voluptate voluptatibus
            possimus, veniam magni quis!</p>
          <a href="#" class="btn btn-primary">Read More &rarr;</a>
        </div>
        <div class="card-footer card-action text-muted">
          Posted on January 1, 2017 by
          <a href="#">Start Bootstrap</a>
        </div>
      </div>

      <!-- Blog Post -->
      <div class="card mb-4">
        <div class="card-image">
          <img class="card-img-top" src="http://placehold.it/750x300" alt="Card image cap">
        </div>
        <div class="card-body card-content">
          <h2 class="card-title">Post Title</h2>
          <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque,
            nulla? Quos cum ex quis soluta, a laboriosam. Dicta expedita corporis animi vero voluptate voluptatibus
            possimus, veniam magni quis!</p>
          <a href="#" class="btn btn-primary">Read More &rarr;</a>
        </div>
        <div class="card-footer card-action text-muted">
          Posted on January 1, 2017 by
          <a href="#">Start Bootstrap</a>
        </div>
      </div>

      <!-- Pagination -->
      <ul class="pagination justify-content-center mb-4">
        <li class="page-item">
          <a class="page-link" href="#">&larr; Older</a>
        </li>
        <li class="page-item disabled">
          <a class="page-link" href="#">Newer &rarr;</a>
        </li>
      </ul>

    </div>

    <!-- Sidebar Widgets Column -->
    <div class="col m4">

      <!-- Search Widget -->
      <div class="card mb-4">
        <h5 class="card-header">Search</h5>
        <div class="card-body card-content">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Search for...">
            <span class="input-group-btn">
              <button class="btn btn-secondary" type="button">Go!</button>
            </span>
          </div>
        </div>
      </div>

      <!-- Categories Widget -->
      <div class="card my-4">
        <h5 class="card-header">Categories</h5>
        <div class="card-body card-content">
          <div class="row">
            <div class="col l6">
              <ul class="list-unstyled mb-0">
                <li>
                  <a href="#">Web Design</a>
                </li>
                <li>
                  <a href="#">HTML</a>
                </li>
                <li>
                  <a href="#">Freebies</a>
                </li>
              </ul>
            </div>
            <div class="col l6">
              <ul class="list-unstyled mb-0">
                <li>
                  <a href="#">JavaScript</a>
                </li>
                <li>
                  <a href="#">CSS</a>
                </li>
                <li>
                  <a href="#">Tutorials</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Side Widget -->
      <div class="card my-4">
        <h5 class="card-header">Side Widget</h5>
        <div class="card-body card-content">
          You can put anything you want inside of these side widgets. They are easy to use, and feature the new
          Bootstrap 4 card containers!
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