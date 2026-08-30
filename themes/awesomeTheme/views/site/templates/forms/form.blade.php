<form method="post" accept-charset="utf-8" action="{{base_url('form/submit')}}"
  @if($siteform->properties)
  @foreach ($siteform->properties as $propertie)
  {{ $propertie->name }}="{{ $propertie->value }}"
  @endforeach
  @endif
  >
  {!! form_hidden('form_reference', $siteform->siteform_id) !!}
  @if($siteform->siteform_items)
  @foreach ($siteform->siteform_items as $item)
  @if($item->item_type == 'textarea')
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">{{$item->item_label}}</label>
    <textarea class="form-control {{$item->item_class}}" name="{{$item->item_name}}"
      id="{{'siteform_item_' . $item->siteform_item_id}}"
      placeholder="{{$item->item_placeholder}}" title="{{$item->item_title}}"
      rows="3" @foreach ($item->properties as $propertie)
    {{ $propertie->name }}="{{ $propertie->value }}"
    @endforeach
    ></textarea>
  </div>
  @elseif ($item->item_type == 'select')
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">{{$item->item_label}}</label>
    <select name="{{$item->item_name}}" id="{{'siteform_item_' . $item->siteform_item_id}}"
      class="{{$item->item_class}}" title="{{$item->item_title}}"
      @foreach ($item->properties as $propertie)
      {{ $propertie->name }}="{{ $propertie->value }}"
      @endforeach
      >
      @foreach ($item->data as $propertie)
      @if($propertie->name == 'select_options')
      @foreach ($propertie->value as $option)
      <option value="{{$option->value}}">{{$option->name}}</option>
      @endforeach
      @endif
      @endforeach
    </select>
  </div>
  @elseif ($item->item_type == 'checkbox')
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">
      <input type="checkbox" name="{{$item->item_name}}" value="1"
        id="{{'siteform_item_' . $item->siteform_item_id}}"
        class="{{$item->item_class}}" title="{{$item->item_title}}"
        @foreach ($item->properties as $propertie)
        {{ $propertie->name }}="{{ $propertie->value }}"
        @endforeach
      >
      {{$item->item_label}}
    </label>
  </div>
  @elseif ($item->item_type == 'hidden')
  <input type="hidden" name="{{$item->item_name}}"
    id="{{'siteform_item_' . $item->siteform_item_id}}"
    @foreach ($item->properties as $propertie)
    {{ $propertie->name }}="{{ $propertie->value }}"
    @endforeach
  >
  @else
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">{{$item->item_label}}</label>
    <input type="{{$item->item_type}}" name="{{$item->item_name}}" class="{{$item->item_class}}"
      id="{{'siteform_item_' . $item->siteform_item_id}}"
      placeholder="{{$item->item_placeholder}}" title="{{$item->item_title}}"
      @foreach ($item->properties as $propertie)
    {{ $propertie->name }}="{{ $propertie->value }}"
    @endforeach
    >
  </div>
  @endif
  @endforeach
  @endif
  <button type="submit" class="btn btn-primary">{{ lang('siteforms_submit') }}</button>
</form>
