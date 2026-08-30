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
            'section' => 'nav',
        ])
        <div class="data-table-filters" v-if="$slots.filters">
            <slot name="filters"></slot>
        </div>
        @include('admin.components.page_navbar', [
            'navbarIf' => 'search_input',
            'section' => 'empty',
            'itemsExpr' => '(pagination ? data : filterData)',
        ])
        <div class="configurations" v-cloak v-if="!loader && data.length > 0">
            <div class="row">
                <div class="col s12">
                    <table class="striped">
                        <thead>
                            <tr>
                                <th v-for="(colum, index) in colums" :key="index" @click="sortData(colum.colum, data);" v-bind:class="getSortData(colum.label)">@{{colum.label}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in (pagination ? data : filterData)" :key="index">
                                <td v-for="(colum, i) in colums" :key="i" >
                                    <span v-if="colum.colum !== 'options'" v-html="getContent(item, colum)"></span>
                                    <span v-else>
                                        <a class='dropdown-trigger' :data-target='"dropdown" + index'><i class="material-icons">more_vert</i></a>
                                        <ul v-if="!options" :id='"dropdown" + index' class='dropdown-content'>
                                            <li><a v-on:click="editItem(item, index);">Edit</a></li>
                                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="setToDeleteItem(item, index);">Delete</a></li>
                                            <li><a v-on:click="archiveItem(item, index);">Archive</a></li>
                                        </ul>
                                        <ul v-else :id='"dropdown" + index' class='dropdown-content'>
                                            <li
                                                v-for="(option, option_index) in options"
                                                :key="option_index">
                                                <a @click="routerPush({option, index, item} )">@{{option.label}}</a>
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
        <div class="container" v-if="!loader && !filter && data.length == 0 && show_empty_input" v-cloak>
            <h4>No hay datos para mostrar</h4>
        </div>
        @include('admin.components.pagination')
        <confirm-modal
                id="deleteModal"
                title="Confirmar Borrar"
                v-on:notify="confirmCallback"
            >
            <p>
                ¿Desea borrar este item?
            </p>
        </confirm-modal>
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped"
            v-on:click="createItem()"
            data-position="left" data-delay="50" data-tooltip="Add item">
                <i class="large material-icons">add</i>
            </a>
        </div>
    </div>
</script>
<script src="https://unpkg.com/vue-router@2.0.0/dist/vue-router.js"></script>