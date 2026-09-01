@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/vendors/lightbox2/src/css/lightbox.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/file_explorer.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/font-awesome/css/all.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/prism/prism.css')?>">
@endsection

@section('content')
<div class="container explorer" id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_files',
        'ledeKey' => 'files_lede',
    ])
    <input type="file" class="hide" ref="replaceInput" @change="replaceFileServe">
    <div class="row">
        <div class="col s12">
            <div class="row">
                <div class="col s12 center" v-bind:class="{ hide: !loader }">
                    <preloader />
                </div>
                <div class="row" v-cloak v-show="!loader">
                    <div class="col s12 m2 tree">
                        <ul class="sidenav hide-on-small-only">
                            <li class="uploadbtn">
                                @if(has_permisions('CREATE_FILE'))
                                <a class="waves-effect waves-teal btn btn-default modal-trigger" href="#uploaderModal">
                                    <i class="material-icons left">file_upload</i> @{{ t('files_add') }}</a>
                                @endif
                            </li>
                            <li><a class="subheader">@{{ t('files_my_drive') }}</a></li>
                            <li><a href="#!" :class="sidebarActive(null)" @click="navigateFiles(root)"><i class="material-icons left">folder</i> @{{ t('files_all') }}</a></li>
                            <li>
                                <a href="#!" :class="sidebarActive('recent')" @click="filterFiles('recent')"><i class="material-icons left">history</i> @{{ t('files_recents') }}</a>
                            </li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('important')" @click="filterFiles('important')"><i class="material-icons left">star</i> @{{ t('files_important') }}</a></li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('trash')" @click="filterFiles('trash')"><i class="material-icons left">delete</i> @{{ t('files_trash') }}</a></li>
                            <li><a class="subheader">@{{ t('files_labels') }}</a></li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('images')" @click="filterFiles('images')"><i class="material-icons left">image</i> @{{ t('files_images') }}</a></li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('docs')" @click="filterFiles('docs')"><i class="material-icons left">description</i> @{{ t('files_docs') }}</a></li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('audio')" @click="filterFiles('audio')"><i class="material-icons left">audiotrack</i> @{{ t('files_audio') }}</a></li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('video')" @click="filterFiles('video')"><i class="material-icons left">movie</i> @{{ t('files_videos') }}</a></li>
                            <li><a class="waves-effect" href="#!" :class="sidebarActive('archives')" @click="filterFiles('archives')"><i class="material-icons left">archive</i> @{{ t('files_archives') }}</a></li>
                        </ul>
                        <ul class="collapsible hide-on-med-and-up">
                            <li>
                                <div class="collapsible-header"><a class="subheader"><i class="material-icons">cloud</i> @{{ t('files_my_drive') }}</a></div>
                                <div class="collapsible-body">
                                    <ul class="suboptions">
                                        <li>
                                            @if(has_permisions('CREATE_FILE'))
                                            <a href="#uploaderModal" class="waves-effect waves-teal modal-trigger">
                                                <i class="material-icons">file_upload</i> @{{ t('files_add') }}</a>
                                            @endif
                                        </li>
                                        <li><a href="#!" class="waves-effect waves-teal" @click="navigateFiles(root)"><i class="material-icons">folder</i> @{{ t('files_all') }}</a></li>
                                        <li><a href="#!" class="waves-effect waves-teal" @click="filterFiles('recent')"><i class="material-icons">history</i> @{{ t('files_recents') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('important')"><i class="material-icons">star</i> @{{ t('files_important') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('trash')"><i class="material-icons">delete</i> @{{ t('files_trash') }}</a></li>
                                        <li><a class="subheader">@{{ t('files_labels') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('images')"><i class="material-icons">image</i> @{{ t('files_images') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('docs')"><i class="material-icons">description</i> @{{ t('files_docs') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('audio')"><i class="material-icons">audiotrack</i> @{{ t('files_audio') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('video')"><i class="material-icons">movie</i> @{{ t('files_videos') }}</a></li>
                                        <li><a class="waves-effect waves-teal" href="#!" @click="filterFiles('archives')"><i class="material-icons">archive</i> @{{ t('files_archives') }}</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col s12 m10 files">
                        <div class="file-overlay" v-bind:class="{ show: showSideRightBar }">
                            <i class="material-icons" @click="setCloseSideRightBar();">close</i>
                            <div class="container">
                                <div class="row">
                                    <div class="col s12">
                                        <div v-if="showFile.file_name">
                                            <h4>@{{showFile.file_name + (showFile.file_type && showFile.file_type !== 'folder' ? '.' + showFile.file_type : '') }}</h4>
                                            <div v-if="showFile.isImagen">
                                                <a :href="getImagePath(showFile)" data-lightbox="roadtrip">
                                                    <img :src="getFullFilePath(showFile)">
                                                </a>
                                            </div>
                                            <div v-else-if="fileIsText(showFile)">
                                                <textarea class="materialize-textarea file-content-editor" rows="18" v-model="showFile.file_content"></textarea>
                                                @if(has_permisions('UPDATE_FILE'))
                                                <button class="btn waves-effect" type="button" @click="saveFileContent()" :disabled="savingContent">@{{ t('files_save') }}</button>
                                                @endif
                                            </div>
                                            <div v-else>
                                                <p>@{{ t('files_no_preview') }}</p>
                                                <a href="#!" class="btn" @click="downloadFile(showFile)">@{{ t('files_download') }}</a>
                                            </div>
                                        </div>
                                        <div class="preloader">
                                            <preloader v-show="!showFile.file_name && showSideRightBar" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row search">
                            <div class="col s12">
                                <nav class="search-nav">
                                    <div class="nav-wrapper">
                                        <div class="input-field">
                                            <input class="input-search" type="search" :placeholder="t('search_files')"
                                                v-model="search" v-on:keyup="searchfiles()">
                                            <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                            <i class="material-icons" @click="resetSearch()">close</i>
                                        </div>
                                        <ul class="right hide-on-med-and-down">
                                            @if(has_permisions('CREATE_FILE'))
                                            <li><a href="#!" @click="startNewFolder()" :title="t('files_new_folder')"><i class="material-icons">create_new_folder</i></a></li>
                                            @endif
                                            <li><a href="#!" v-on:click="listView = !listView"><i class="material-icons">@{{ listView ? 'view_module' : 'view_list' }}</i></a></li>
                                            <li><a href="#!" v-on:click="reloadFileExplorer();"><i class="material-icons">refresh</i></a></li>
                                        </ul>
                                    </div>
                                </nav>
                                <nav v-if="curDir != root || getbreadcrumb.length" class="navigation-nav">
                                    <div class="nav-wrapper">
                                        <div class="col s12 breadcrumb-nav">
                                            <a href="#!" class="breadcrumb" @click="navigateFiles(root)"><i class="material-icons">home</i></a>
                                            <a href="#!" class="breadcrumb" v-for="(item, index) in getbreadcrumb"
                                                :key="index"
                                                @click="navigateFiles(item.path)">@{{item.folder}}</a>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                        <div class="file-bulk-bar" v-if="selectedCount">
                            <span>@{{ selectedCount }} @{{ t('files_selected') }}</span>
                            <a href="#!" @click="selectAllVisible()">@{{ t('files_select_all') }}</a>
                            <a href="#!" @click="clearSelection()">@{{ t('files_clear_selection') }}</a>
                            <a href="#!" @click="downloadZip()"><i class="material-icons left">archive</i> @{{ t('files_download_zip') }}</a>
                            @if(has_permisions('UPDATE_FILE'))
                            <a class="modal-trigger" href="#folderSelectorMove" @click="fileToMove = {}">@{{ t('files_move') }}</a>
                            @endif
                            @if(has_permisions('DELETE_FILE'))
                            <a class="modal-trigger" href="#deleteFileModal" @click="trashSelected()">@{{ t('files_delete') }}</a>
                            @endif
                        </div>
                        <div class="row filelist" :class="{ 'list-view': listView }">
                            <div class="col s12 center" v-bind:class="{ hide: !fileloader }">
                                <preloader />
                            </div>
                            <div v-bind:class="{ hide: fileloader }" v-if="recentlyFiles.length && !activeFilter && curDir == root">
                                <div class="col s12">
                                    <h5>@{{ t('files_recently') }}</h5>
                                </div>
                                <div class="col s12 m6 l4 xl3" v-for="(item, index) in recentlyFiles" :key="'r'+item.file_id" @click="setSideRightBarSelectedFile(item)">
                                    <div class="card">
                                        <div class="card-image">
                                            <div class="icon">
                                                <i :class="getIcon(item)"></i>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <p>@{{item.file_name}}@{{getExtention(item)}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-bind:class="{ hide: fileloader }" v-if="getFolders.length">
                                <div class="col s12">
                                    <h5>@{{ t('files_folders') }}</h5>
                                </div>
                                <div class="col s12 m6 l4 xl3 folder" v-for="(item, index) in getFolders" :key="'f'+item.file_id"
                                    :class="{ selected: isSelected(item) }">
                                    <label class="checkbox" @click.stop>
                                        <input type="checkbox" :checked="isSelected(item)" @change="toggleSelect(item, $event)">
                                        <span>&nbsp;</span>
                                    </label>
                                    <a class="grey-text text-darken-4 dropdown-trigger folder-menu" href="#!"
                                        :data-target="'folder_options' + item.file_id" @click.stop><i class="material-icons right">more_vert</i></a>
                                    <ul :id="'folder_options' + item.file_id" class="dropdown-content">
                                        @if(has_permisions('UPDATE_FILE'))
                                        <li><a class="modal-trigger" href="#modal1" @click="renameFile(item)">@{{ t('files_rename') }}</a></li>
                                        <li><a class="modal-trigger" href="#folderSelectorMove" @click="setFileToMove(item)">@{{ t('files_move') }}</a></li>
                                        @endif
                                        <li><a href="#!" @click="downloadZip([item.file_id])">@{{ t('files_download_zip') }}</a></li>
                                        @if(has_permisions('DELETE_FILE'))
                                        <li><a class="modal-trigger" href="#deleteFileModal" @click="trashFile(item)">@{{ t('files_delete') }}</a></li>
                                        @endif
                                    </ul>
                                    <div class="card-panel" @click="navigateFiles(item.file_path + item.file_name + '/')">
                                        <div class="card-icon">
                                            <div class="icon">
                                                <i class="material-icons">folder</i>
                                            </div>
                                        </div>
                                        <div class="card-content">
                                            <span>@{{item.file_name}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col s12 m6 l4 xl3 folder new-folder" v-if="creatingFolder">
                                    <div class="card-panel">
                                        <div class="card-icon"><i class="material-icons">folder</i></div>
                                        <div class="card-content">
                                            <input type="text" ref="folderNameInput" v-model="newFolderName"
                                                v-on:keyup.enter="makeFolderServer()" v-on:blur="makeFolderServer()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getFiles.length" v-bind:class="{ hide: fileloader }">
                                <div class="col s12">
                                    <h5>@{{ t('files_files') }}</h5>
                                </div>
                                <div class="col s12">
                                    <div class="row">
                                        <div class="col s12 m6 l4 xl3 file" v-for="(item, index) in getFiles"
                                            :key="'file'+item.file_id" :class="{ selected: isSelected(item) }">
                                            <div class="card">
                                                <label class="checkbox file-check" @click.stop>
                                                    <input type="checkbox" :checked="isSelected(item)" @change="toggleSelect(item, $event)">
                                                    <span>&nbsp;</span>
                                                </label>
                                                <a class="grey-text text-darken-4 dropdown-trigger" href="#!"
                                                    :data-target="'file_options' + item.file_id"><i class="material-icons right">more_vert</i></a>
                                                <ul :id="'file_options' + item.file_id" class="dropdown-content"
                                                    v-if="!inTrash">
                                                    <li><a href="#!" @click="setSideRightBarSelectedFile(item)">
                                                            <i class="material-icons">visibility</i> @{{ t('files_view') }}</a>
                                                    </li>
                                                    <li><a href="#!" @click="downloadFile(item)">
                                                            <i class="material-icons">file_download</i> @{{ t('files_download') }}</a>
                                                    </li>
                                                    @if(has_permisions('UPDATE_FILE'))
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger" href="#modal1"
                                                            @click="renameFile(item);">
                                                            <i class="material-icons">edit</i>
                                                            @{{ t('files_rename') }}</a>
                                                    </li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            @click="setFileToMove(item);" href="#folderSelectorMove">
                                                            <i class="material-icons">content_cut</i>
                                                            @{{ t('files_move') }}</a>
                                                    </li>
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            @click="setFileToMove(item);" href="#folderSelectorCopy">
                                                            <i class="material-icons">content_copy</i>
                                                            @{{ t('files_copy') }}</a>
                                                    </li>
                                                    <li><a href="#!" @click="featuredFileServe(item);">
                                                            <i class="material-icons">@{{ item.featured == 1 ? 'star' : 'star_border' }}</i>
                                                            @{{ t('files_important') }}</a></li>
                                                    @endif
                                                    @if(has_permisions('DELETE_FILE'))
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            href="#deleteFileModal" @click="trashFile(item);">
                                                            <i class="material-icons">delete</i> @{{ t('files_delete') }}
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                                <ul :id="'file_options' + item.file_id" class="dropdown-content" v-else>
                                                    @if(has_permisions('UPDATE_FILE'))
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            @click="setFileToMove(item);" href="#folderSelectorMove">
                                                            <i class="material-icons">content_cut</i>
                                                            @{{ t('files_move') }}</a>
                                                    </li>
                                                    @endif
                                                    @if(has_permisions('DELETE_FILE'))
                                                    <li>
                                                        <a class="waves-effect waves-light modal-trigger"
                                                            href="#deleteFileModal" @click="trashFile(item);">
                                                            <i class="material-icons">delete</i> @{{ t('files_delete') }}
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                                <div class="card-image waves-effect waves-block waves-light"
                                                    v-if="!isImage(item)" @click="setSideRightBarSelectedFile(item)">
                                                    <div class="file icon activator">
                                                        <i :class="getIcon(item)"></i>
                                                    </div>
                                                </div>
                                                <div class="card-image" v-if="isImage(item)">
                                                    <a :href="getImagePath(item)" data-lightbox="roadtrip"><img
                                                            :src="getImagePath(item)"></a>
                                                </div>
                                                <div class="card-content" @click="setSideRightBarSelectedFile(item);">
                                                    @{{(item.file_name + getExtention(item)) | shortName}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="getFiles.length == 0 && getFolders.length == 0 && !fileloader">
                                <div class="row">
                                    <div class="col s12 file-empty">
                                        <h5>@{{ t('files_empty') }}</h5>
                                        <p>@{{ t('files_empty_cta') }}</p>
                                        @if(has_permisions('CREATE_FILE'))
                                        <a class="btn modal-trigger" href="#uploaderModal">@{{ t('files_add') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="file-info-colum side-right" v-bind:class="{ show: showSideRightBar }"
                            v-if="showSideRightBar">
                            <div class="side-header">
                                <span
                                    class="filename">@{{(sideRightBarSelectedFile.file_name + getExtention(sideRightBarSelectedFile))}}</span>
                                @if(has_permisions('DELETE_FILE'))
                                <a class="waves-effect waves-light modal-trigger right" href="#deleteFileModal"
                                    @click="trashFile(sideRightBarSelectedFile);">
                                    <i class="material-icons grey-text text-darken-4">delete</i></a>
                                @endif
                            </div>
                            <br />
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs" id="filetabs">
                                        <li class="tab col s6"><a class="active" href="#fileinfo"><i
                                                    class="material-icons">info_outline</i> @{{ t('files_info') }}</a></li>
                                        <li class="tab col s6"><a href="#filehistory"><i
                                                    class="material-icons">linear_scale</i> @{{ t('files_history') }}</a></li>
                                    </ul>
                                </div>
                                <div class="tabsbody">
                                    <div id="fileinfo" class="col s12">
                                        <div class="preview">
                                            <div class="card-image" v-if="!isImage(sideRightBarSelectedFile)">
                                                <div class="file icon ">
                                                    <i :class="getIcon(sideRightBarSelectedFile)"></i>
                                                </div>
                                                <div class="divider"></div>
                                            </div>
                                        </div>
                                        <ul class="file_options">
                                            <li>
                                                <a href="#!" @click="downloadFile(sideRightBarSelectedFile)"><i
                                                        class="material-icons">file_download</i> @{{ t('files_download') }}</a>
                                            </li>
                                            <li><a href="#!" @click="copyFileLink(sideRightBarSelectedFile)">
                                                    <i class="material-icons">link</i> @{{ t('files_copy_link') }}</a>
                                            </li>
                                            @if(has_permisions('UPDATE_FILE'))
                                            <li>
                                                <a class="waves-effect waves-light modal-trigger" href="#modal1"
                                                    @click="renameFile(sideRightBarSelectedFile);">
                                                    <i class="material-icons">edit</i>
                                                    @{{ t('files_rename') }}</a>
                                            </li>
                                            <li><a href="#!" @click="pickReplaceFile(sideRightBarSelectedFile)">
                                                    <i class="material-icons">swap_horiz</i> @{{ t('files_replace') }}</a></li>
                                            <li><a href="#!" @click="featuredFileServe(sideRightBarSelectedFile);">
                                                    <i class="material-icons">@{{ sideRightBarSelectedFile.featured == 1 ? 'star' : 'star_border' }}</i>
                                                    @{{ t('files_important') }}</a></li>
                                            @endif
                                            @if(has_permisions('DELETE_FILE'))
                                            <li v-if="!inTrash">
                                                <a class="waves-effect waves-light modal-trigger"
                                                    href="#deleteFileModal"
                                                    @click="trashFile(sideRightBarSelectedFile);">
                                                    <i class="material-icons">delete</i> @{{ t('files_delete') }}
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                        <ul class="collection">
                                            <li class="collection-item">@{{ t('files_type') }}: <span
                                                    class="secondary-content">@{{sideRightBarSelectedFile.file_type}}</span>
                                            </li>
                                            <li class="collection-item">@{{ t('files_created') }}: <span
                                                    class="secondary-content">@{{timeAgo(sideRightBarSelectedFile.date_create)}}</span>
                                            </li>
                                            <li class="collection-item">@{{ t('files_updated') }}: <span
                                                    class="secondary-content">@{{timeAgo(sideRightBarSelectedFile.date_update)}}</span>
                                            </li>
                                            <li class="collection-item">@{{ t('files_folder') }}: <span
                                                    class="secondary-content">@{{(sideRightBarSelectedFile.file_path)}}</span>
                                            </li>
                                            <li class="collection-item">@{{ t('files_key') }}: <span
                                                    class="secondary-content">@{{(sideRightBarSelectedFile.rand_key)}}</span>
                                            </li>
                                            <li class="collection-item" v-if="sideRightBarSelectedFile.user_group">
                                                @{{ t('files_shared_with') }}: <span
                                                    class="secondary-content">@{{sideRightBarSelectedFile.user_group.name}}</span>
                                            </li>
                                            <li class="collection-item" v-if="sideRightBarSelectedFile.user">
                                                @{{ t('files_created_by') }}: <br />
                                                <user-info :user="sideRightBarSelectedFile.user" />
                                            </li>
                                        </ul>

                                    </div>
                                    <div id="filehistory" class="col s12" style="display: none;">
                                        <ul class="collection filehistory">
                                            <li class="collection-item avatar"
                                                v-for="(history, index) in sideRightBarSelectedFile.history"
                                                :key="index">
                                                <i
                                                    class="material-icons circle teal">@{{getFileHistoryIcon(history.action)}}</i>
                                                <span class="title">@{{history.description}}</span>
                                                <p>
                                                    <span v-if="history.user">@{{history.user.get_fullname()}}</span>
                                                    <br>
                                                    @{{timeAgo(history.date_create)}}
                                                </p>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal1" class="modal">
        <div class="modal-content">
            <h4>@{{ t('files_rename') }}</h4>
            <div class="input-field col s6">
                <input :placeholder="t('files_rename')" id="file_rename_input" type="text" v-on:keyup.enter="renameFileServe()"
                    v-model="editFile.new_name" class="validate">
                <label for="file_rename_input">@{{ t('files_rename') }}</label>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" @click="renameFileServe()" class="modal-close waves-effect waves-green btn-flat">@{{ t('files_agree') }}</a>
        </div>
    </div>
    <div id="deleteFileModal" class="modal">
        <div class="modal-content">
            <h4>@{{ t('files_delete') }}</h4>
            <p v-if="!inTrash">@{{ moveToTrash && moveToTrash.bulk ? t('files_confirm_trash_many') : t('files_confirm_trash') }}</p>
            <p v-else>@{{ t('files_confirm_delete') }}</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">@{{ t('cancel') }}</a>
            <a href="#!" v-if="!inTrash" @click="confirmTrash();"
                class="modal-close waves-effect waves-green btn-flat">@{{ t('files_move') }}</a>
            <a href="#!" v-else @click="confirmDelete();"
                class="modal-close waves-effect waves-green btn-flat red white-text">@{{ t('files_delete') }}</a>
        </div>
    </div>
    <file-explorer-selector :modal="'folderSelectorMove'" :preselected="[]" :mode="'folders'" :multiple="false"
        v-on:notify="moveCallcack">
    </file-explorer-selector>
    <file-explorer-selector :modal="'folderSelectorCopy'" :preselected="[]" :mode="'folders'" :multiple="false"
        v-on:notify="copyCallcack">
    </file-explorer-selector>

</div>
<div id="uploaderModal" class="modal bottom-sheet">
    <div class="modal-content">
        <h4><?php echo lang('upload_files'); ?></h4>
        <input type="file" id="input-100" name="input-100[]" accept="*" multiple>
    </div>
    <div class="modal-footer">
        <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat"><?php echo lang('cancel'); ?></a>
    </div>
</div>

@include('admin.components.file_explorer_selector_component')

@endsection


@section('footer_includes')
<script src="<?=base_url('resources/components/FileExplorerSelector.js');?>"></script>
<script src="<?=base_url('resources/components/FileExplorerModule.js');?>"></script>
<script src="<?=base_url('public/vendors/lightbox2/src/js/lightbox.js');?>"></script>
<script src="<?=base_url('public/vendors/fileinput/js/fileinput.min.js');?>"></script>
<script src="<?=base_url('public/vendors/fileinput/js/plugins/canvas-to-blob.min.js');?>"></script>
<script src="<?=base_url('public/vendors/fileinput/js/locales/es.js');?>"></script>
<script src="<?=base_url('public/vendors/prism/prism.js');?>"></script>

<script>
$(document).on('ready', function() {
    $("#input-100").fileinput({
        uploadUrl: BASEURL + "admin/files/ajax_upload_file",
        enableResumableUpload: true,
        resumableUploadOptions: {},
        uploadExtraData: {
            'uploadToken': 'SOME-TOKEN',
            'curDir': './uploads'
        },
        showCancel: true,
        initialPreview: [],
        deleteUrl: "",
        overwriteInitial: false,
        progressClass: 'determinate progress-bar bg-success progress-bar-success progress-bar-striped active',
        progressInfoClass: 'determinate progress-bar bg-info progress-bar-info progress-bar-striped active',
        progressCompleteClass: 'determinate progress-bar bg-success progress-bar-success',
        progressPauseClass: 'determinate progress-bar bg-primary progress-bar-primary progress-bar-striped active',
        progressErrorClass: 'determinate progress-bar bg-danger progress-bar-danger',
    }).on('fileuploaded', function(event, previewId, index, fileId) {
        FileExplorerModule.reloadFileExplorer();
        FileExplorerModule.toast("files_uploaded");
        M.Modal.getInstance($('#uploaderModal')[0]).close();
    });
});
</script>
@endsection
