@extends('site.layouts.site')

@section('title', $title)

@section('headers_includes')
<link rel="stylesheet" href="{{base_url(getThemePublicPath())}}css/web-development.css">
@endsection

@section('content')
<div id="home" class="header-hero bg_cover" style="background-image: url({{base_url(getThemePublicPath())}}images/banner-bg.svg)">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="header-hero-content text-center">
                    <h3 class="header-sub-title wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="0.2s" style="visibility: visible; animation-duration: 1.3s; animation-delay: 0.2s; animation-name: fadeInUp;">Basic - SaaS Landing Page</h3>
                    <h2 class="header-title wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="0.5s" style="visibility: visible; animation-duration: 1.3s; animation-delay: 0.5s; animation-name: fadeInUp;">Kickstart Your SaaS or App Site</h2>
                    <p class="text wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="0.8s" style="visibility: visible; animation-duration: 1.3s; animation-delay: 0.8s; animation-name: fadeInUp;">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor</p>
                    <a href="#" class="main-btn wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="1.1s" style="visibility: visible; animation-duration: 1.3s; animation-delay: 1.1s; animation-name: fadeInUp;">Get Started</a>
                </div> <!-- header hero content -->
            </div>
        </div> <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="header-hero-image text-center wow fadeIn" data-wow-duration="1.3s" data-wow-delay="1.4s" style="visibility: visible; animation-duration: 1.3s; animation-delay: 1.4s; animation-name: fadeIn;">
                    <img src="assets/images/header-hero.png" alt="hero">
                </div> <!-- header hero image -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->
    <div id="particles-1" class="particles"><canvas class="particles-js-canvas-el" width="1898" height="1028" style="width: 100%; height: 100%;"></canvas></div>
</div>
@endsection