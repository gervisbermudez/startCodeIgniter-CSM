@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
<!-- Page Content -->
  <div>

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">Full Width
      <small>Subheading</small>
    </h1>

    <div class="section">
      <nav class="breadcrumbs">
        <div class="nav-wrapper">
          <div class="col s12">
            <a href="{{base_url()}}" class="breadcrumb">Home</a>
            <a href="#!" class="breadcrumb">Full Width</a>
          </div>
        </div>
      </nav>
    </div>

    <p>Most of Start Bootstrap's unstyled templates can be directly integrated into the Modern Business template. You can view all of our unstyled templates on our website at
      <a href="http://startbootstrap.com/template-categories/unstyled">http://startbootstrap.com/template-categories/unstyled</a>.</p>

  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
