<?=doctype('html5')?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<main>
    <h1>{{ lang('events_heading') }}</h1>
    @if($when === 'past')
        <p><a href="{{ base_url('events') }}">{{ lang('events_heading') }}</a></p>
    @else
        <p><a href="{{ base_url('events?when=past') }}">{{ lang('events_past_heading') }}</a></p>
    @endif

    @if($events && count($events))
        <ul>
            @foreach($events as $event)
                <li>
                    @include('site.templates.event_card', ['event' => $event])
                </li>
            @endforeach
        </ul>
    @else
        <p>{{ lang('events_no_upcoming') }}</p>
    @endif

    @if(!empty($past_events) && count($past_events) && $when !== 'past')
        <h2>{{ lang('events_past_heading') }}</h2>
        <ul>
            @foreach($past_events as $event)
                <li>
                    @include('site.templates.event_card', ['event' => $event])
                </li>
            @endforeach
        </ul>
    @endif
</main>
</body>
</html>
