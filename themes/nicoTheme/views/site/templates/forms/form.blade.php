<form method="post" accept-charset="utf-8" action="{{base_url('form/submit')}}" @foreach ($siteform->properties as
  $propertie)
  {{$propertie->name}}="{{$propertie->value}}"
  @endforeach
  >
  {!! form_hidden('form_reference', $siteform->siteform_id) !!}
  @foreach ($siteform->siteform_items as $item)
  @if($item->item_type == 'textarea')
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">{{$item->item_label}}</label>
    <textarea class="form-control" name="{{$item->item_name}}" id="{{'siteform_item_' . $item->siteform_item_id}}"
      rows="3" @foreach ($item->properties as $propertie)
    {{$propertie->name}}="{{$propertie->value}}"
    @endforeach  
    ></textarea>
  </div>
  @elseif ($item->item_type == 'select')
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">{{$item->item_label}}</label>
    <select name="{{$item->item_name}}" id="{{'siteform_item_' . $item->siteform_item_id}}"
      class="{{$item->item_class}}" @foreach ($item->properties as $propertie)
      {{$propertie->name}}="{{$propertie->value}}"
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
  @else
  <div class="form-group">
    <label for="{{'siteform_item_' . $item->siteform_item_id}}">{{$item->item_label}}</label>
    <input type="{{$item->item_type}}" name="{{$item->item_name}}" class="{{$item->item_class}}"
      id="{{'siteform_item_' . $item->siteform_item_id}}" @foreach ($item->properties as $propertie)
    {{$propertie->name}}="{{$propertie->value}}"
    @endforeach
    >
  </div>
  @endif
  @endforeach
  <button type="submit" class="btn btn-primary">Submit Message</button>
</form>