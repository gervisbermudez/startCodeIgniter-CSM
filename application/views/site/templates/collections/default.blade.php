<section class="scms-collection scms-collection--default">
  @if (!empty($collection->name))
    <h2 class="scms-collection-title">{{ $collection->name }}</h2>
  @endif
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      <h3>{{ $item->title }}</h3>
      @if (!empty($item->fields))
        @foreach ($item->fields as $key => $value)
          @if ($key !== 'title' && !is_object($value) && !is_array($value) && $value !== '' && $value !== null)
            <p class="scms-collection-field scms-collection-field--{{ $key }}">{{ $value }}</p>
          @endif
        @endforeach
      @endif
    </article>
  @endforeach
</section>
