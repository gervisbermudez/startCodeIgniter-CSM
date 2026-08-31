<section class="scms-collection scms-collection--cards">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @php
        $image = collection_item_image($item);
        $text = collection_item_field($item, array('text', 'card_content', 'content', 'body'));
        if (is_object($text) || is_array($text)) {
            $text = '';
        }
      @endphp
      @if (!empty($image) && !empty($image->url))
        <img src="{{ $image->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @if ($text !== '' && $text !== null)
        <p>{{ $text }}</p>
      @endif
    </article>
  @endforeach
</section>
