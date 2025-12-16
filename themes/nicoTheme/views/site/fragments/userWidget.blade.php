@section("user_widget")
<div class="about-author d-flex p-4 bg-light">
    <div class="bio mr-5">
        <img src="<?php echo base_url($page->user->user_data['avatar']); ?>" alt="{{$page->user->user_data['nombre']. '
        '. $page->user->user_data['apellido']}}" class="img-fluid mb-4">
    </div>
    <div class="desc">
        <h3><a href="{{base_url('blog/author/' . $page->user->username)}}">{{$page->user->user_data['nombre']. ' '.
                $page->user->user_data['apellido']}}</a></h3>
        {!! fragment("about_me") !!}
    </div>
</div>
@endsection