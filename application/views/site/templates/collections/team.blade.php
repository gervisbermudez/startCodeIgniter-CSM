<section class="scms-collection scms-collection--team">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @php
        $photo = null;
        if (!empty($item->fields['image']) && is_object($item->fields['image'])) {
            $photo = $item->fields['image'];
        } elseif (!empty($item->fields['photo']) && is_object($item->fields['photo'])) {
            $photo = $item->fields['photo'];
        }
        $role = '';
        if (!empty($item->fields['role']) && !is_object($item->fields['role'])) {
            $role = $item->fields['role'];
        } elseif (!empty($item->fields['job']) && !is_object($item->fields['job'])) {
            $role = $item->fields['job'];
        } elseif (!empty($item->fields['position']) && !is_object($item->fields['position'])) {
            $role = $item->fields['position'];
        }
      @endphp
      @if (!empty($photo) && !empty($photo->url))
        <img src="{{ $photo->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
      @if ($role !== '')
        <p class="scms-collection-role">{{ $role }}</p>
      @endif
    </article>
  @endforeach
</section>
