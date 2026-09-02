@if(!empty($perm) && has_permisions($perm))
<a href="{{ $href }}"
    class="dash-widget-add tooltipped"
    data-position="top"
    data-delay="50"
    data-tooltip="{{ $tip }}"
    aria-label="{{ $tip }}">
    <i class="material-icons">add</i>
</a>
@endif
