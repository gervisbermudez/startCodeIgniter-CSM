<section class="scms-collection scms-collection--faq">
  <dl>
    @foreach ($collection->items as $item)
      @php
        $question = $item->title;
        if (!empty($item->fields['question']) && !is_object($item->fields['question'])) {
            $question = $item->fields['question'];
        }
        $answer = '';
        if (!empty($item->fields['answer']) && !is_object($item->fields['answer'])) {
            $answer = $item->fields['answer'];
        } elseif (!empty($item->fields['text']) && !is_object($item->fields['text'])) {
            $answer = $item->fields['text'];
        }
      @endphp
      <div class="scms-collection-item">
        <dt>{{ $question }}</dt>
        @if ($answer !== '')
          <dd>{{ $answer }}</dd>
        @endif
      </div>
    @endforeach
  </dl>
</section>
