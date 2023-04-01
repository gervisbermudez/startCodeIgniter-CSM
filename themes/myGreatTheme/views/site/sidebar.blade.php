@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
  <!-- Page Content -->
  <div class="container">

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Sidebar Page
      <small>Subheading</small>
    </h1>

    <div class="section">
      <nav class="breadcrumbs">
        <div class="nav-wrapper">
          <div class="col s12">
            <a href="{{base_url()}}" class="breadcrumb">Home</a>
            <a href="#!" class="breadcrumb">Sidebar Page</a>
          </div>
        </div>
      </nav>
    </div>

    <!-- Content Row -->
    <div class="row">
      <!-- Sidebar Column -->
      <div class="col l3 mb-4">
        <div class="list-group collection">
          <a href="index.html" class="list-group-item collection-item">Home</a>
          <a href="about.html" class="list-group-item collection-item">About</a>
          <a href="services.html" class="list-group-item collection-item">Services</a>
          <a href="contact.html" class="list-group-item collection-item">Contact</a>
          <a href="portfolio-1-col.html" class="list-group-item collection-item">1 Column Portfolio</a>
          <a href="portfolio-2-col.html" class="list-group-item collection-item">2 Column Portfolio</a>
          <a href="portfolio-3-col.html" class="list-group-item collection-item">3 Column Portfolio</a>
          <a href="portfolio-4-col.html" class="list-group-item collection-item">4 Column Portfolio</a>
          <a href="portfolio-item.html" class="list-group-item collection-item">Single Portfolio Item</a>
          <a href="blog-home-1.html" class="list-group-item collection-item">Blog Home 1</a>
          <a href="blog-home-2.html" class="list-group-item collection-item">Blog Home 2</a>
          <a href="blog-post.html" class="list-group-item collection-item">Blog Post</a>
          <a href="full-width.html" class="list-group-item collection-item">Full Width Page</a>
          <a href="sidebar.html" class="list-group-item collection-item active">Sidebar Page</a>
          <a href="faq.html" class="list-group-item collection-item">FAQ</a>
          <a href="404.html" class="list-group-item collection-item">404</a>
          <a href="pricing.html" class="list-group-item collection-item">Pricing Table</a>
        </div>
      </div>
      <!-- Content Column -->
      <div class="col l8 mb-4">
        <h2>Section Heading</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta, et temporibus, facere perferendis veniam beatae non debitis, numquam blanditiis necessitatibus vel mollitia dolorum laudantium, voluptate dolores iure maxime ducimus fugit.</p>
      </div>
    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
