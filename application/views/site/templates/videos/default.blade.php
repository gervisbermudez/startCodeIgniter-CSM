<div class="site-embed site-embed-video">
    @if(!empty($video->nam))
    <h3 class="site-embed-video__title">{{ $video->nam }}</h3>
    @endif
    @if(!empty($youtube_id))
    <div class="site-embed-video__frame">
        <iframe
            src="https://www.youtube.com/embed/{{ $youtube_id }}"
            title="{{ !empty($video->nam) ? $video->nam : 'YouTube' }}"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
        ></iframe>
    </div>
    @endif
    @if(!empty($video->description))
    <div class="site-embed-video__description">{!! $video->description !!}</div>
    @endif
</div>
