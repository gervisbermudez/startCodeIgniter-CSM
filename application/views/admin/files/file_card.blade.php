@php
    $menuId = isset($menuId) ? $menuId : 'file_options';
@endphp
<div class="card">
    <label class="checkbox file-check" @click.stop>
        <input type="checkbox" :checked="isSelected(item)" @change="toggleSelect(item, $event)">
        <span>&nbsp;</span>
    </label>
    <a class="grey-text text-darken-4 dropdown-trigger" href="#!"
        :data-target="'{{ $menuId }}' + item.file_id"
        @click.stop><i class="material-icons right">more_vert</i></a>
    <ul :id="'{{ $menuId }}' + item.file_id" class="dropdown-content"
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
    <ul :id="'{{ $menuId }}' + item.file_id" class="dropdown-content" v-else>
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
                :src="getImagePath(item)" :alt="item.file_name + getExtention(item)"></a>
    </div>
    <div class="card-content" @click="setSideRightBarSelectedFile(item);"
        :title="item.file_name + getExtention(item)">
        @{{ item.file_name + getExtention(item) }}
    </div>
</div>
