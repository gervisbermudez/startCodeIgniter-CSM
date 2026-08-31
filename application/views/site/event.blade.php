<?=doctype('html5')?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<main>
    <p><a href="{{ base_url('events') }}">{{ lang('events_heading') }}</a></p>
    <article>
        <h1>{{ $event->name }}</h1>
        @if(!empty($event->subtitle))
            <p>{{ $event->subtitle }}</p>
        @endif
        @php
            $allDay = !empty($event->all_day);
            $start = !empty($event->date_start) ? ($allDay ? substr($event->date_start, 0, 10) : substr($event->date_start, 0, 16)) : '';
            $end = !empty($event->date_end) ? ($allDay ? substr($event->date_end, 0, 10) : substr($event->date_end, 0, 16)) : '';
        @endphp
        @if($start)
            <p><time datetime="{{ $event->date_start }}">{{ $start }}@if($end) – {{ $end }}@endif</time></p>
        @endif
        @if(($event->location_type === 'physical' || $event->location_type === 'hybrid') && !empty($event->address))
            <p>{{ $event->address }}</p>
        @endif
        @if(($event->location_type === 'online' || $event->location_type === 'hybrid') && !empty($event->online_url))
            <p><a href="{{ $event->online_url }}">{{ $event->online_url }}</a></p>
        @elseif($event->location_type === 'online')
            <p>{{ lang('events_online') }}</p>
        @endif
        @if(!empty($event->imagen_file) && is_object($event->imagen_file) && !empty($event->imagen_file->file_front_path))
            <p><img src="{{ $event->imagen_file->file_front_path }}" alt="{{ $event->name }}"></p>
        @endif
        <div>{!! $event->content !!}</div>
    </article>
</main>
</body>
</html>
