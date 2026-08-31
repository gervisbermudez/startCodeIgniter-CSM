<section class="scms-collection scms-collection--team">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @php
        $photo = collection_item_image($item);
        $role = collection_item_field($item, array('role', 'job', 'position', 'cargo'));
        if (is_object($role) || is_array($role)) {
            $role = '';
        }
      @endphp
      @if (!empty($photo) && !empty($photo->url))
        <img src="{{ $photo->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @if ($role !== '' && $role !== null)
        <p class="scms-collection-role">{{ $role }}</p>
      @endif
    </article>
  @endforeach
</section>
