<div class="site-embed site-embed-event">
    @if(!empty($event->name))
    <h3 class="site-embed-event__title">{{ $event->name }}</h3>
    @endif
    @if(!empty($event->date_publish))
    <p class="site-embed-event__date">{{ $event->date_publish }}</p>
    @endif
    @if(!empty($event->address))
    <p class="site-embed-event__address">{{ $event->address }}</p>
    @endif
    @if(!empty($excerpt))
    <p class="site-embed-event__excerpt">{{ $excerpt }}</p>
    @endif
</div>
