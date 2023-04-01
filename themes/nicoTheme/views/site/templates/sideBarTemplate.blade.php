@extends('site.layouts.' . $layout)

@section('title', $title)

@section('footer')
@include('site.shared.navbar')
@endsection

@include('site.fragments.recentsBlogs')
@include('site.fragments.userWidget')
@include('site.fragments.pageComments')

@section('content')
<section class="hero-wrap hero-wrap-2"
    style="background-image: url(<?php echo base_url($page->main_image->file_front_path); ?>);"
    data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-center">
            <div class="col-md-9 ftco-animate pb-5 text-center">
                <h1 class="mb-3 bread">{{$page->title}}</h1>
                <p class="breadcrumbs"><span class="mr-2"><a href="{{base_url()}}">Home <i
                                class="ion-ios-arrow-forward"></i></a></span> <span class="mr-2"><a
                            href="{{base_url('blog')}}">Blog </a>
                </p>
            </div>
        </div>
    </div>
</section>
<section class="ftco-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 ftco-animate">
                <h2 class="mb-3">
                    {{$page->subtitle}}
                </h2>
                <?php echo $page->content ?>
                <div class="tag-widget post-tag-container mb-5 mt-5">
                    <div class="tagcloud">
                    @if(isset($page->page_data['tags']))
                        @foreach ($page->page_data['tags'] as $tag)
                        <a href="{{base_url('blog/tag/' . $tag)}}" class="tag-cloud-link">{{$tag}}</a>
                        @endforeach
                    @endif
                    </div>
                </div>
                @yield("user_widget")
                @if(config('SHOW_BLOG_COMMENTS') == "Si")
                    @yield("page_comments")
                @endif

            </div> <!-- .col-md-8 -->
            @include("site.fragments.blogSidebar")
        </div>
    </div>
</section> <!-- .section -->
@endsection



@section('footer')
@include('site.shared.footer')
@endsection