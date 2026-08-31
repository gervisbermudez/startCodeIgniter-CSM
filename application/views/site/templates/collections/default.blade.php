<section class="scms-collection scms-collection--default">
  @if (!empty($collection->name))
    <h2 class="scms-collection-title">{{ $collection->name }}</h2>
  @endif
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @php
        $image = collection_item_image($item);
      @endphp
      @if (!empty($image) && !empty($image->url))
        <img src="{{ $image->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @if (!empty($item->fields))
        @foreach ($item->fields as $key => $value)
          @if ($key !== 'title' && $key !== 'image' && $key !== 'imagen' && $key !== 'photo' && $key !== 'picture' && !is_object($value) && !is_array($value) && $value !== '' && $value !== null)
            <p class="scms-collection-field scms-collection-field--{{ $key }}">{{ $value }}</p>
          @endif
        @endforeach
      @endif
    </article>
  @endforeach
</section>
