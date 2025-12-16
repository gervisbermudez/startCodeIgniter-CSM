@section("recents_blogs")
<div class="sidebar-box ftco-animate">
    <h3 class="heading-sidebar">Recent Blog</h3>
    <div id="recentBlogs">
    @if(isset($blogs))
    @forelse ($blogs as $blog)
        <div class="block-21 mb-4 d-flex">
        @if (isset($blog->thumbnail_image))
        <a class="blog-img mr-4"
                style="background-image: url(<?php echo base_url($blog->thumbnail_image->file_full_path); ?>"></a>
        @elseif (isset($blog->imagen_file))
        <a class="blog-img mr-4"
                style="background-image: url(<?php echo base_url($blog->imagen_file->file_full_path); ?>"></a>
        
                @else
            <a class="blog-img mr-4"
                style="background-image: url(<?php echo base_url(getThemePublicPath()); ?>images/image_1.jpg);"></a>
                @endif
            <div class="text">
                <h3 class="heading"><a href="{{base_url($blog->path)}}">{{ $blog->title }}</a></h3>
                <div class="meta">
                    <div><a href="#"><span class="icon-calendar"></span> {{time_ago($blog->date_publish)}}</a></div>
                    <div><a href="#"><span class="icon-person"></span> {{ $blog->user->user_data["nombre"] . ' ' . $blog->user->user_data["apellido"]}}</a></div>
                    <div><a href="#"><span class="icon-chat"></span> 19</a></div>
                </div>
            </div>
        </div>
        @empty
        <p>No Blogs</p>
    @endforelse
    @endif
    </div>
</div>
@endsection