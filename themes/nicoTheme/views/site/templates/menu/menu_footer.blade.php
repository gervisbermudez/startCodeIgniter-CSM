<ul class="list-unstyled footer_links_ul">
@if($menu->menu_items)
    @foreach ($menu->menu_items as $item)
    @if(!$item->subitems)
    <li class="footer_links_li {{isSectionActive($item->item_name, 1)}}">
        <a
        class="footer_links_a"
        href="{{$item->item_link}}"
        target="{{$item->item_target}}"
        title="{{$item->item_title}}"
        >
        <span class="icon-long-arrow-right mr-2"></span>
        {{$item->item_label}}
        </a>
    </li>
    @endif
    @endforeach
@endif
</ul>
