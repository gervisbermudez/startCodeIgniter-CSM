<section class="scms-collection scms-collection--cards">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @if (!empty($item->fields['image']) && is_object($item->fields['image']) && !empty($item->fields['image']->url))
        <img src="{{ $item->fields['image']->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @php
        $text = '';
        if (!empty($item->fields['text']) && !is_object($item->fields['text'])) {
            $text = $item->fields['text'];
        } elseif (!empty($item->fields['card_content']) && !is_object($item->fields['card_content'])) {
            $text = $item->fields['card_content'];
        }
      @endphp
      @if ($text !== '')
        <p>{{ $text }}</p>
      @endif
    </article>
  @endforeach
</section>
