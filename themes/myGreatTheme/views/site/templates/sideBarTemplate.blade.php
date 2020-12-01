@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
@include('site.shared.navbar')
@endsection

@section('content')
<!-- Page Content -->
<div class="container">

    <!-- Page Heading/Breadcrumbs -->
    <h1 class="mt-4 mb-3">{{$page->title}}
        <small>by
            <a href="#"></a>
        </small>
    </h1>

    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{base_url()}}">Home</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{base_url('blog')}}">Blog</a></li>
    </ol>

    <div class="row">

        <!-- Post Content Column -->
        <div class="col l8">
            @if ($page->main_image)
            <!-- Preview Image -->
            <img class="responsive-img rounded" src="{{base_url($page->main_image->file_front_path)}}" alt="{{$page->title}}">

            <hr>
            @endif
            <!-- Date/Time -->
            <p>Posted on </p>

            <hr>

            <?= $page->content ?>

        </div>

        <!-- Sidebar Widgets Column -->
        <div class="col m4">

            <!-- Search Widget -->
            <div class="card mb-4">
                <h5 class="card-header">Search</h5>
                <div class="card-body">
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
                <div class="card-body">
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
                <div class="card-body">
                    You can put anything you want inside of these side widgets. They are easy to use, and feature the new Bootstrap 4 card containers!
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
