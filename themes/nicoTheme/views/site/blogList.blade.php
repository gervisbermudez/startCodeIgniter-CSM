@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
    @include('site.shared.navbar')
@endsection

@section('content')
  <!-- Page Content -->
  <div class="container">
    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">
      <div>My Blog</div>
      <small>See my new discoveries, latest projects and featured blogs.</small>
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{base_url()}}">Home</a>
      </li>
      <li class="breadcrumb-item active">Blog</li>
    </ol>
    <div class="row">
      <!-- Blog Entries Column -->
      <div class="col m8">
      @if(count($blogs))

      @foreach ($blogs as $blog)
        <!-- Blog Post -->
        <div class="card mb-4">
          @if(isset($blog->imagen_file))
          <img class="card-img-top" src="/{{$blog->imagen_file->file_front_path}}" alt="Card image cap">
          @endif
          <div class="card-body card-content">
            <h2 class="card-title"><a href="{{base_url($blog->path)}}">{{$blog->title}}</a></h2>
            <p class="card-text">
            {{character_limiter(strip_tags($blog->content), 250)}}
            </p>
            <a href="{{base_url($blog->path)}}" class="btn btn-primary">Read More &rarr;</a>
            <br/>
            <br/>
            <div class="card-footer text-muted">
              Posted on {{time_ago($blog->date_create)}} by
              <a href="{{base_url('blog/author/' . $blog->user->username)}}">{{$blog->user->username}}</a>
            </div>
          </div>
        </div>
      @endforeach
      @else
      Nothing here...
      @endif

      @if(count($blogs) > 5)
        <!-- Pagination -->
        <ul class="pagination justify-content-center mb-4">
          <li class="page-item disabled">
            <a class="page-link" href="#">&larr; Older</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="#">Newer &rarr;</a>
          </li>
        </ul>
        @endif
      </div>

      @include("site.fragments.blogSidebar")
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container -->
@endsection

@section('footer')
    @include('site.shared.footer')
@endsection
