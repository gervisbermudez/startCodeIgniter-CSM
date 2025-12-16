<ul class="right hide-on-med-and-down">
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
            class="nav-link dropdown-toggle dropdown-trigger {{isSectionActive($item->item_name, 1)}}"
            id="navbarDropdown{{$item->item_name}}{{$item->menu_item_id}}"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            href="#!"
            target="{{$item->item_target}}"
            title="{{$item->item_title}}"
            data-target="dropdown{{$item->item_name}}{{$item->menu_item_id}}"
            >
            {{$item->item_label}}
        </a>
        <ul
        aria-labelledby="navbarDropdown{{$item->item_name}}{{$item->menu_item_id}}"
        id="dropdown{{$item->item_name}}{{$item->menu_item_id}}"
        class="dropdown-content dropdown-menu dropdown-menu-right"
        >
        <a
            class="nav-link dropdown-toggle {{isSectionActive($item->item_name, 1)}}"
            href="{{$item->item_link}}"
            target="{{$item->item_target}}"
            title="{{$item->item_title}}"
            >
            {{$item->item_label}}
        </a>
        @foreach ($item->subitems as $subitem)
            <li>
                <a
                class="dropdown-item"
                href="{{$subitem->item_link}}"
                target="{{$subitem->item_target}}"
                title="{{$subitem->item_title}}"
                >
                {{$subitem->item_label}}
                </a>
            </li>
        @endforeach
        </ul>
    </li>
    @endif
    @endforeach
@endif
</ul>
