@php
    $allDay = !empty($event->all_day);
    $start = !empty($event->date_start) ? ($allDay ? substr($event->date_start, 0, 10) : substr($event->date_start, 0, 16)) : '';
    $place = '';
    if (!empty($event->location_type) && $event->location_type === 'online') {
        $place = lang('events_online');
    } elseif (!empty($event->address)) {
        $place = $event->address;
    }
@endphp
<article class="event-card">
    @if(!empty($event->imagen_file) && is_object($event->imagen_file) && !empty($event->imagen_file->file_front_path))
        <img src="{{ $event->imagen_file->file_front_path }}" alt="{{ $event->name }}">
    @endif
    <h2><a href="{{ base_url('events/' . $event->slug) }}">{{ $event->name }}</a></h2>
    @if($start)
        <p><time datetime="{{ $event->date_start }}">{{ $start }}</time></p>
    @endif
    @if($place)
        <p>{{ $place }}</p>
    @endif
    <p><a href="{{ base_url('events/' . $event->slug) }}">{{ lang('events_read_more') }}</a></p>
</article>
