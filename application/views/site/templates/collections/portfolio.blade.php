<section class="scms-collection scms-collection--portfolio">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @php
        $image = collection_item_image($item);
        $url = collection_item_url($item);
      @endphp
      @if (!empty($image) && !empty($image->url))
        <img src="{{ $image->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @if ($url !== '')
        <a class="scms-collection-link" href="{{ $url }}">{{ $item->title }}</a>
      @endif
    </article>
  @endforeach
</section>
