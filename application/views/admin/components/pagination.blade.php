{{-- Paginador Materialize. Requiere mixin: showPagination, paginatorLinks, pagerTo. --}}
<div class="row list-pagination" v-if="showPagination && paginator.total_pages > 1" v-cloak>
    <div class="col s12">
        <ul class="pagination">
            <li v-for="(link, index) in paginatorLinks" :key="'page-link-' + index" :class="link.class">
                <a v-if="link.class !== 'disabled'" href="#!" v-on:click.prevent="pagerTo(link.page);" v-html="link.label"></a>
                <a v-else href="#!" v-html="link.label"></a>
            </li>
        </ul>
        <p class="grey-text text-darken-1" v-if="paginator.total_rows">
            {{ lang('pagination_page_of') }} @{{ paginator.current_page }} / @{{ paginator.total_pages }}
            (@{{ paginator.total_rows }})
        </p>
    </div>
</div>
