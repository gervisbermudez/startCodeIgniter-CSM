@extends('site.layouts.' . $layout)

@section('title', $title)

@section('content')
<div class="container">
    <div class="article">
        <h1 class="mt-4 mb-3">{{$page->title}}<small>{{$page->subtitle}}</small></h1>
        <?= $page->content ?>
    </div>
</div>
@endsection

@section('headers_includes')
@isset($headers_includes)
<?php echo $headers_includes ?>
@endisset
@endsection

@section('footer_includes')
@isset($footer_includes)
<?php echo $footer_includes ?>
@endisset
@endsection
