@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div id="root">
    <div class="col s12 center" v-show="loader">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'pages-search',
        'refreshMethod' => 'getPages(currentStatus)',
        'itemsExpr' => 'filterAll',
    ])
    <div class="row">
        <div class="col s12">
            <div class="status-filters" v-cloak v-show="!loader">
        <div class="status-chip" :class="{active: currentStatus === null}" @click="getPages(null)">
            All Active
        </div>
        <div class="status-chip" :class="{active: currentStatus === 1}" @click="getPages(1)">
            Published
        </div>
        <div class="status-chip" :class="{active: currentStatus === 2}" @click="getPages(2)">
            Drafts
        </div>
        <div class="status-chip" :class="{active: currentStatus === 3}" @click="getPages(3)">
            Archived
        </div>
        <div class="status-chip" :class="{active: currentStatus === 0}" @click="getPages(0)">
            Trash (Deleted)
        </div>
    </div>
        </div>
    </div>
    <div class="pages" v-cloak v-if="!loader && pages.length > 0">
        <div class="row">
            <div class="col s12">
                <h4>Results</h4>
            </div>
        </div>
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Page Title</th>
                            <th>Path</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(page, index) in filterAll" :key="index">
                            <td>
                                <img :src="getPageImagePath(page)" class="circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>@{{truncate(page.title, 50)}}</td>
                            <td><a :href="base_url('admin/pages/view/' + page.page_id)">@{{page.path}}</a></td>
                            <td>
                                <a v-if="page.user" :href="base_url('admin/users/ver/' + page.user_id)">@{{page.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{page.date_publish ? page.date_publish : page.date_create}}
                            </td>
                            <td>
                                <span v-if="page.status == 1 && isFuturePublish(page)" class="custom-badge status-scheduled">
                                    <i class="material-icons tiny">schedule</i> <?= lang('scheduled') ?>
                                </span>
                                <span v-else-if="page.status == 1" class="custom-badge status-published">
                                    <i class="material-icons tiny">check_circle</i> Published
                                </span>
                                <span v-else-if="page.status == 2" class="custom-badge status-draft">
                                    <i class="material-icons tiny">edit</i> Draft
                                </span>
                                <span v-else-if="page.status == 3" class="custom-badge status-archived">
                                    <i class="material-icons tiny">archive</i> Archived
                                </span>
                                <span v-else-if="page.status == 0" class="custom-badge status-deleted">
                                    <i class="material-icons tiny">delete_outline</i> Deleted
                                </span>
                            </td>
                            <td>
                                <span v-if="page.visibility == 1" class="custom-badge visibility-public">
                                    <i class="material-icons tiny">public</i> Public
                                </span>
                                <span v-else class="custom-badge visibility-private">
                                    <i class="material-icons tiny">lock</i> Private
                                </span>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + page.page_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + page.page_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/pages/view/' + page.page_id)">Preview</a></li>
                                    @if(has_permisions('UPDATE_PAGE'))
                                    <li><a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a></li>
                                    @endif
                                    @if(has_permisions('DELETE_PAGE'))
                                    <li v-if="page.status != 0"><a class="modal-trigger" href="#deleteModal"
                                            v-on:click="setTempPage(page, index);">Delete</a></li>
                                    @endif
                                    @if(has_permisions('CREATE_PAGE'))
                                    <li><a href="#!" @click.prevent="duplicatePage(page)">Duplicate</a></li>
                                    @endif
                                    <li v-if="page.status != 3"><a class="modal-trigger" href="#archiveModal"
                                            v-on:click="setTempPage(page, index);">Archive</a></li>
                                    <li v-if="page.path"><a :href="base_url(page.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row pages-grid" v-else>
            <div class="col s12 m4" v-for="(page, index) in filterAll" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(page)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown" + page.page_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + page.page_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/pages/view/' + page.page_id)">Preview</a></li>
                            @if(has_permisions('UPDATE_PAGE'))
                            <li><a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a></li>
                            @endif
                            @if(has_permisions('DELETE_PAGE'))
                            <li><a class="modal-trigger" href="#deleteModal"
                                    v-on:click="setTempPage(page, index);">Delete</a></li>
                            @endif
                            @if(has_permisions('CREATE_PAGE'))
                            <li><a href="#!" @click.prevent="duplicatePage(page)">Duplicate</a></li>
                            @endif
                            <li v-if="page.status != 3"><a class="modal-trigger" href="#archiveModal"
                                    v-on:click="setTempPage(page, index);"><?php echo lang('archive'); ?></a></li>
                            <li v-if="page.path"><a :href="base_url(page.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a
                                    :href="base_url('admin/pages/view/' + page.page_id)">@{{truncate(page.title, 50)}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'page'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(page)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info :user="page.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{truncate(page.title, 50)}}
                        </span>
                        <span class="subtitle">
                            @{{page.subtitle}}
                        </span>
                        <ul>
                            <li><b>Publish Date:</b> <br>
                                @{{page.date_publish ? page.date_publish : page.date_create}}</li>
                            <li><b>Category:</b> @{{page.categorie}}</li>
                            <li><b>Subcategory:</b> @{{page.subcategorie ? page.subcategorie : 'None'}}</li>
                            <li><b>Template:</b> @{{page.template}}</li>
                            <li><b>Type:</b> @{{page.page_type_name}}</li>
                            <li><b>Status:</b>
                                <span v-if="page.status == 1 && isFuturePublish(page)"><?= lang('scheduled') ?></span>
                                <span v-else-if="page.status == 1">Published</span>
                                <span v-else-if="page.status == 2">Draft</span>
                                <span v-else-if="page.status == 3">Archived</span>
                                <span v-else-if="page.status == 0">Deleted</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pages" v-cloak v-if="!loader && pages.length > 0 && !filterAll.length">
        <div class="row">
            <div class="col s12">
                <h4>Pages</h4>
            </div>
        </div>
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Page Title</th>
                            <th>Path</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(page, index) in filterPages" :key="index">
                            <td>
                                <img :src="getPageImagePath(page)" class="circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>@{{truncate(page.title, 50)}}</td>
                            <td><a :href="base_url('admin/pages/view/' + page.page_id)">@{{page.path}}</a></td>
                            <td>
                                <a v-if="page.user" :href="base_url('admin/users/ver/' + page.user_id)">@{{page.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{page.date_publish ? page.date_publish : page.date_create}}
                            </td>
                            <td>
                                <span v-if="page.status == 1 && isFuturePublish(page)" class="custom-badge status-scheduled">
                                    <i class="material-icons tiny">schedule</i> <?= lang('scheduled') ?>
                                </span>
                                <span v-else-if="page.status == 1" class="custom-badge status-published">
                                    <i class="material-icons tiny">check_circle</i> Published
                                </span>
                                <span v-else-if="page.status == 2" class="custom-badge status-draft">
                                    <i class="material-icons tiny">edit</i> Draft
                                </span>
                                <span v-else-if="page.status == 3" class="custom-badge status-archived">
                                    <i class="material-icons tiny">archive</i> Archived
                                </span>
                                <span v-else-if="page.status == 0" class="custom-badge status-deleted">
                                    <i class="material-icons tiny">delete_outline</i> Deleted
                                </span>
                            </td>
                            <td>
                                <span v-if="page.visibility == 1" class="custom-badge visibility-public">
                                    <i class="material-icons tiny">public</i> Public
                                </span>
                                <span v-else class="custom-badge visibility-private">
                                    <i class="material-icons tiny">lock</i> Private
                                </span>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + page.page_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + page.page_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/pages/view/' + page.page_id)">Preview</a></li>
                                    @if(has_permisions('UPDATE_PAGE'))
                                    <li><a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a></li>
                                    @endif
                                    <li v-if="page.status == 0 || page.status == 3"><a href="#!" @click.prevent="restorePage(page)">Restore</a></li>
                                    @if(has_permisions('DELETE_PAGE'))
                                    <li v-if="page.status != 0"><a class="modal-trigger" href="#deleteModal"
                                            v-on:click="setTempPage(page, index);">Delete</a></li>
                                    @endif
                                    <li v-if="page.status != 3"><a class="modal-trigger" href="#archiveModal"
                                            v-on:click="setTempPage(page, index);">Archive</a></li>
                                    <li v-if="page.path"><a :href="base_url(page.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row pages-grid" v-else>
            <div class="col s12 m4" v-for="(page, index) in filterPages" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(page)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown" + page.page_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + page.page_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/pages/view/' + page.page_id)">Preview</a></li>
                            @if(has_permisions('UPDATE_PAGE'))
                            <li><a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a></li>
                            @endif
                            @if(has_permisions('DELETE_PAGE'))
                            <li><a class="modal-trigger" href="#deleteModal"
                                    v-on:click="setTempPage(page, index);">Delete</a></li>
                            @endif
                            @if(has_permisions('CREATE_PAGE'))
                            <li><a href="#!" @click.prevent="duplicatePage(page)">Duplicate</a></li>
                            @endif
                            <li v-if="page.status != 3"><a class="modal-trigger" href="#archiveModal"
                                    v-on:click="setTempPage(page, index);"><?php echo lang('archive'); ?></a></li>
                            <li v-if="page.path"><a :href="base_url(page.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a
                                    :href="base_url('admin/pages/view/' + page.page_id)">@{{truncate(page.title, 50)}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'page'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(page)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info :user="page.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{truncate(page.title, 50)}}
                        </span>
                        <span class="subtitle">
                            @{{page.subtitle}}
                        </span>
                        <ul>
                            <li><b>Publish Date:</b> <br>
                                @{{page.date_publish ? page.date_publish : page.date_create}}</li>
                            <li><b>Category:</b> @{{page.categorie}}</li>
                            <li><b>Subcategory:</b> @{{page.subcategorie ? page.subcategorie : 'None'}}</li>
                            <li><b>Template:</b> @{{page.template}}</li>
                            <li><b>Type:</b> @{{page.page_type_name}}</li>
                            <li><b>Status</b>
                                <span v-if="page.status == 1 && isFuturePublish(page)">
                                    <?= lang('scheduled') ?>
                                </span>
                                <span v-else-if="page.status == 1">
                                    <?= lang('published') ?>
                                </span>
                                <span v-else>
                                    Draft
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pages" v-cloak v-if="!loader && blogs.length > 0 && !filterAll.length">
        <div class="row">
            <div class="col s12">
                <h4>Blogs</h4>
            </div>
        </div>
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Page Title</th>
                            <th>Path</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(page, index) in blogs" :key="index">
                            <td>
                                <img :src="getPageImagePath(page)" class="circle" style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td>@{{truncate(page.title, 50)}}</td>
                            <td class="truncate"><a
                                    :href="base_url('admin/pages/view/' + page.page_id)">@{{page.path}}</a></td>
                            <td>
                                <a v-if="page.user" :href="base_url('admin/users/ver/' + page.user_id)">@{{page.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{page.date_publish ? page.date_publish : page.date_create}}
                            </td>
                            <td>
                                <span v-if="page.status == 1 && isFuturePublish(page)" class="custom-badge status-scheduled">
                                    <i class="material-icons tiny">schedule</i> <?= lang('scheduled') ?>
                                </span>
                                <span v-else-if="page.status == 1" class="custom-badge status-published">
                                    <i class="material-icons tiny">check_circle</i> Published
                                </span>
                                <span v-else class="custom-badge status-draft">
                                    <i class="material-icons tiny">edit</i> Draft
                                </span>
                            </td>
                            <td>
                                <span v-if="page.visibility == 1" class="custom-badge visibility-public">
                                    <i class="material-icons tiny">public</i> Public
                                </span>
                                <span v-else class="custom-badge visibility-private">
                                    <i class="material-icons tiny">lock</i> Private
                                </span>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + page.page_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + page.page_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/pages/view/' + page.page_id)">Preview</a></li>
                                    @if(has_permisions('UPDATE_PAGE'))
                                    <li><a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a></li>
                                    @endif
                                    <li v-if="page.status == 0 || page.status == 3"><a href="#!" @click.prevent="restorePage(page)">Restore</a></li>
                                    @if(has_permisions('DELETE_PAGE'))
                                    <li v-if="page.status != 0"><a class="modal-trigger" href="#deleteModal"
                                            v-on:click="setTempPage(page, index);">Delete</a></li>
                                    @endif
                                    @if(has_permisions('CREATE_PAGE'))
                                    <li><a href="#!" @click.prevent="duplicatePage(page)">Duplicate</a></li>
                                    @endif
                                    <li v-if="page.status != 3"><a class="modal-trigger" href="#archiveModal"
                                            v-on:click="setTempPage(page, index);">Archive</a></li>
                                    <li v-if="page.path"><a :href="base_url(page.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row pages-grid" v-else>
            <div class="col s12 m4" v-for="(page, index) in blogs" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(page)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown" + page.page_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + page.page_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/pages/view/' + page.page_id)">Preview</a></li>
                            @if(has_permisions('UPDATE_PAGE'))
                            <li><a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a></li>
                            @endif
                            @if(has_permisions('DELETE_PAGE'))
                            <li><a class="modal-trigger" href="#deleteModal"
                                    v-on:click="setTempPage(page, index);">Delete</a></li>
                            @endif
                            @if(has_permisions('CREATE_PAGE'))
                            <li><a href="#!" @click.prevent="duplicatePage(page)">Duplicate</a></li>
                            @endif
                            <li v-if="page.status != 3"><a class="modal-trigger" href="#archiveModal"
                                    v-on:click="setTempPage(page, index);"><?php echo lang('archive'); ?></a></li>
                            <li v-if="page.path"><a :href="base_url(page.path)" target="_blank"><?php echo lang('view_in_site'); ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a
                                    :href="base_url('admin/pages/view/' + page.page_id)">@{{truncate(page.title, 50)}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'page'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(page)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info :user="page.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{truncate(page.title, 50)}}
                        </span>
                        <span class="subtitle">
                            @{{page.subtitle}}
                        </span>
                        <ul>
                            <li><b>Publish Date:</b> <br>
                                @{{page.date_publish ? page.date_publish : page.date_create}}</li>
                            <li><b>Category:</b> @{{page.categorie}}</li>
                            <li><b>Subcategory:</b> @{{page.subcategorie ? page.subcategorie : 'None'}}</li>
                            <li><b>Template:</b> @{{page.template}}</li>
                            <li><b>Type:</b> @{{page.page_type_name}}</li>
                            <li><b>Status:</b>
                                <span v-if="page.status == 1 && isFuturePublish(page)"><?= lang('scheduled') ?></span>
                                <span v-else-if="page.status == 1">Published</span>
                                <span v-else-if="page.status == 2">Draft</span>
                                <span v-else-if="page.status == 3">Archived</span>
                                <span v-else-if="page.status == 0">Deleted</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && pages.length == 0 && !filter" v-cloak>
        <h4>No pages found</h4>
    </div>
    @include('admin.components.pagination')
    <confirm-modal id="deleteModal" title="Confirm Delete" v-on:notify="confirmDelete">
        <p>
            Do you want to delete this Page?
        </p>
    </confirm-modal>
    <confirm-modal id="archiveModal" title="Confirm Archive" v-on:notify="confirmArchive">
        <p>
            Do you want to archive this Page?
        </p>
    </confirm-modal>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
        data-delay="50" data-tooltip="New Page" href="{{base_url('admin/pages/nueva/')}}">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/PagesLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection