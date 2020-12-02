<script type="text/x-template" id="dataTableComponent-template">
<div id="dataTableComponent-root">
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    <nav class="page-navbar" v-cloak v-show="!loader && data.length > 0">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                    <input class="input-search" type="search" placeholder="Buscar..." v-model="filter">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li><a v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>

                <li><a v-on:click="getData();"><i class="material-icons">refresh</i></a></li>
                <li>
                    <a class='dropdown-trigger' data-target='dropdown-options'><i class="material-icons">more_vert</i></a>
                    <ul id='dropdown-options' class='dropdown-content'>
                        <li><a href="#!">Archivo</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
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
                        <tr v-for="(item, index) in data" :key="index">
							<td v-for="(colum, i) in colums" :key="i" >
								<span v-if="colum.colum !== 'options'" v-html="getContent(item, colum)"></span>
								<span v-else>
									<a class='dropdown-trigger' :data-target='"dropdown" + index'><i class="material-icons">more_vert</i></a>
									<ul :id='"dropdown" + index' class='dropdown-content'>
										<li><a v-on:click="editItem(item, index);">Edit</a></li>
										<li><a class="modal-trigger" href="#deleteModal" v-on:click="deleteItem(item, index);">Delete</a></li>
										<li><a href="#!" v-on:click="archiveItem(item, index);">Archive</a></li>
									</ul>
								</span>
							</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-if="showPagination">
            <div class="col s12">
                <ul class="pagination">
                    <li v-for="(link, index) in paginatorLinks" :key="index" :class="link.class">
                        <a v-if="link.class !== 'disabled'" v-on:click="pagerTo(link.page);" v-html="link.label"></a>
                        <a v-else v-html="link.label"></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && data.length == 0" v-cloak>
        <h4>No hay datos para mostrar</h4>
    </div>
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
