<!-- Sidebar Widgets Column -->
<div class="col-lg-4 sidebar ftco-animate" id="root">
    <div class="sidebar-box">
        <form action="{{base_url('blog/search/')}}" class="search-form">
            <div class="form-group">
                <span class="icon icon-search"></span>
                <input name="q" type="text" class="form-control" placeholder="Type a keyword and hit enter">
            </div>
        </form>
    </div>
    <div class="sidebar-box ftco-animate">
        <h3 class="heading-sidebar">Categories</h3>
        <ul class="categories">
            @if(isset($categories))
            @forelse ($categories as $categorie)
            <li><a href="{{base_url('blog/categorie/' . $categorie->name)}}">{{$categorie->name}}</a> <!-- <span>(12)</span> --></a></li>
            @empty
            @endforelse
            @endif
        </ul>
    </div>

    @yield("recents_blogs")

    <div class="sidebar-box ftco-animate">
        <h3 class="heading-sidebar">Tag Cloud</h3>
        <div class="tagcloud">
            @if(isset($tags))
            @forelse ($tags as $tag)
            <a href="{{base_url('blog/tag/' . $tag)}}" class="tag-cloud-link">{{$tag}}</a>
            @empty
            @endforelse
            @endif
        </div>
    </div>
    <div class="sidebar-box ftco-animate">
        {!! fragment("blog_sidebar_paragraf") !!}
    </div>
</div>