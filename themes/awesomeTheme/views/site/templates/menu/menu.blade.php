<ul class="navbar-nav ml-auto menu_parent">
@if($menu->menu_items)
    @foreach ($menu->menu_items as $item)
    @if(!$item->subitems)
    <li class="nav-item {{isSectionActive($item->item_name, 1)}}">
        <a 
        class="nav-link" 
        href="{{$item->item_link}}" 
        target="{{$item->item_target}}" 
        title="{{$item->item_title}}"
        >
        {{$item->item_label}}
        </a>
    </li>
    @else
    <li class="nav-item dropdown">
        <a 
            class="nav-link dropdown-toggle {{isSectionActive($item->item_name, 1)}}" 
            id="navbarDropdown{{$item->item_name}}{{$item->menu_item_id}}" 
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            href="{{$item->item_link}}" 
            target="{{$item->item_target}}" 
            title="{{$item->item_title}}"
            >
            {{$item->item_label}}
        </a>
        <div 
        class="dropdown-menu dropdown-menu-right" 
        aria-labelledby="navbarDropdown{{$item->item_name}}{{$item->menu_item_id}}"
        >
        @foreach ($item->subitems as $subitem)
            <a 
            class="dropdown-item" 
            href="{{$subitem->item_link}}" 
            target="{{$subitem->item_target}}" 
            title="{{$subitem->item_title}}"
            >
            {{$subitem->item_label}}
            </a>
        @endforeach
        </div>
    </li>
    @endif
    @endforeach
@endif
</ul>