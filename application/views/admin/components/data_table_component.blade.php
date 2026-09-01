<script type="text/x-template" id="dataTableComponent-template">
    <div id="dataTableComponent-root">
        <div class="col s12 center" v-show="loader">
            <br><br>
            <preloader />
        </div>
        @include('admin.components.page_navbar', [
            'searchInputId' => 'datatable-search',
            'refreshMethod' => 'getData()',
            'showViewToggle' => false,
            'navbarIf' => 'search_input',
            'section' => 'nav-open',
        ])
        <div class="page-navbar__filters" v-if="$slots.filters || hasStatusFilters">
            <slot name="filters"></slot>
            <div class="filter-group" v-if="hasStatusFilters" role="group">
                <button
                    type="button"
                    class="status-chip"
                    v-for="chip in statusFilters"
                    :key="'st-' + String(chip.value)"
                    :class="{ active: chipFilter === chip.value }"
                    @click="setStatusFilter(chip.value)"
                >@{{ chip.label }}</button>
            </div>
        </div>
        @include('admin.components.page_navbar', [
            'refreshMethod' => 'getData()',
            'showViewToggle' => false,
            'navbarIf' => 'search_input',
            'section' => 'nav-close',
        ])
        @include('admin.components.page_navbar', [
            'navbarIf' => 'search_input',
            'section' => 'empty',
            'itemsExpr' => 'tableRows',
        ])
        <div class="configurations" v-cloak v-if="!loader && tableRows.length > 0">
            <div class="row">
                <div class="col s12">
                    <table class="striped">
                        <thead>
                            <tr>
                                <th v-for="(colum, index) in colums" :key="index" @click="sortData(colum.colum, data);" v-bind:class="getSortData(colum.label)">@{{colum.label}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in tableRows" :key="index">
                                <td v-for="(colum, i) in colums" :key="i" >
                                    <span v-if="colum.colum !== 'options'" v-html="getContent(item, colum)"></span>
                                    <span v-else>
                                        <a class='dropdown-trigger' :data-target='"dropdown" + index'><i class="material-icons">more_vert</i></a>
                                        <ul v-if="!options" :id='"dropdown" + index' class='dropdown-content'>
                                            <li v-if="can_update"><a v-on:click="editItem(item, index);">Edit</a></li>
                                            <li v-if="can_delete"><a href="#!" v-on:click.prevent="openDeleteModal(item, index);">Delete</a></li>
                                            <li v-if="can_update"><a v-on:click="archiveItem(item, index);">Archive</a></li>
                                        </ul>
                                        <ul v-else :id='"dropdown" + index' class='dropdown-content'>
                                            <li
                                                v-for="(option, option_index) in options"
                                                :key="option_index"
                                                v-if="option.action !== 'delete' || can_delete">
                                                <a v-if="option.action === 'delete'" href="#!" @click.prevent="openDeleteModal(item, index)">
                                                    <i v-if="option.icon" class="material-icons left">@{{option.icon}}</i>
                                                    @{{option.label}}
                                                </a>
                                                <a v-else @click="runOption(option, index, item)">
                                                    <i v-if="option.icon" class="material-icons left">@{{option.icon}}</i>
                                                    @{{option.label}}
                                                </a>
                                            </li>
                                        </ul>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="container" v-if="!loader && !filter && data.length == 0 && show_empty_input && !isStatusChipActive" v-cloak>
            <h4 class="page-header">@{{ empty_title || 'No hay datos para mostrar' }}</h4>
            <a v-if="empty_cta && empty_href" class="btn waves-effect" :href="empty_href" style="background-color: var(--st-accent);">@{{ empty_cta }}</a>
        </div>
        @include('admin.components.list_filter_empty', [
            'showExpr' => '!loader && data.length == 0 && isStatusChipActive && !filter',
            'clearMethod' => 'clearChipFilter()',
        ])
        @include('admin.components.pagination')
        <confirm-modal
                id="deleteModal"
                :title="confirm_title || 'Confirmar Borrar'"
                v-on:notify="confirmCallback"
            >
            <p>
                @{{ confirm_body || '¿Desea borrar este item?' }}
            </p>
        </confirm-modal>
        <div class="fixed-action-btn" v-if="show_fab" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large waves-effect new tooltipped"
            v-bind:class="fab_accent ? 'st-fab' : 'red'"
            v-bind:style="fab_accent ? { backgroundColor: 'var(--st-accent)' } : {}"
            v-on:click="createItem()"
            data-position="left" data-delay="50" :data-tooltip="fab_tooltip || 'Add item'">
                <i class="large material-icons">add</i>
            </a>
        </div>
    </div>
</script>
<script src="https://unpkg.com/vue-router@2.0.0/dist/vue-router.js"></script>