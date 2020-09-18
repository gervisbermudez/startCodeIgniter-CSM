@extends('site.layouts.' . $layout)

@section('title', $title)

@section('content')
<div class="container">
    <h1>
        {{$page->title}}
    </h1>
    <h2>
        {{$page->subtitle}}
    </h2>
    <div class="article">
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
