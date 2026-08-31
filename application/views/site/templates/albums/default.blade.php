<div class="site-embed site-embed-album">
    @if(!empty($album->name))
    <h3 class="site-embed-album__title">{{ $album->name }}</h3>
    @endif
    @if(!empty($album->description))
    <div class="site-embed-album__description">{!! $album->description !!}</div>
    @endif
    @if(!empty($items))
    <div class="site-embed-album__grid">
        @foreach ($items as $item)
        @if(!empty($item->file) && !empty($item->file->file_front_path))
        <figure class="site-embed-album__item">
            <img src="{{ $item->file->file_front_path }}" alt="{{ $item->name }}" />
            @if(!empty($item->name))
            <figcaption>{{ $item->name }}</figcaption>
            @endif
        </figure>
        @endif
        @endforeach
    </div>
    @endif
</div>
