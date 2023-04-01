@extends('site.layouts.' . $layout)

@section('title', $title)

@section('content')
<div class="container">
    <section>
        <div class="ftco-animate">
            <h1 class="mb-3 bread">{{$page->title}}</h1>
        </div>
        <h2 class="mb-3">
            {{$page->subtitle}}
        </h2>
        <div class="article">
            <?=$page->content?>
        </div>
    </section>
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