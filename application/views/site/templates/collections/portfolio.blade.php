<section class="scms-collection scms-collection--portfolio">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @if (!empty($item->fields['image']) && is_object($item->fields['image']) && !empty($item->fields['image']->url))
        <img src="{{ $item->fields['image']->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @php
        $url = '';
        if (!empty($item->fields['url']) && !is_object($item->fields['url'])) {
            $url = $item->fields['url'];
        } elseif (!empty($item->fields['link']) && !is_object($item->fields['link'])) {
            $url = $item->fields['link'];
        }
      @endphp
      @if ($url !== '')
        <a class="scms-collection-link" href="{{ $url }}">{{ $item->title }}</a>
      @endif
    </article>
  @endforeach
</section>
