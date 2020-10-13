<script type="text/x-template" id="fileExplorerCollection-template">
<div class="panel fileExplorerCollection-root">
    <div class="title indigo">
        <h5>Archivos</h5>
        <div class="subtitle">
            @{{files.length}} Archivos
        </div>
        <a class='dropdown-trigger' href='#' data-target='fileExplorerFilter'>
            <i class="material-icons">more_vert</i>
        </a>
        <ul id='fileExplorerFilter' class='dropdown-content'>
        <li><a href="#!" @click="getFiles();">All</a></li>
        <li><a href="#!" @click="filterFiles('images');">Imagenes</a></li>
        <li><a href="#!" @click="filterFiles('docs');">Documents</a></li>
        <li><a href="#!" @click="filterFiles('video');">Videos</a></li>
        <li><a href="#!" @click="filterFiles('important');">Important</a></li>
        <li><a href="#!" @click="filterFiles('trash');">Trash</a></li>
        </ul>
    </div>
    <ul class="collection">
        <li  v-if="files.length" class="collection-item avatar" v-for="(file, index) in shortFiles" :key="index">
            <i class="material-icons circle red">insert_drive_file</i>
            <a :href="file.get_full_file_path()" class="item-title">@{{file.get_filename()}}</a>
            <p>
            <a :href="file.get_full_share_path()" class="item-title">Share File</a>
            <br>
                @{{timeAgo(file.date_create)}}
            </p>
            <a href="#!" class="secondary-content" :class="{'yellow-text': file.featured == '1'}" v-on:click="featuredFileServe(file);"><i class="material-icons">grade</i></a>
        </li>
        <li v-else class="collection-item">
            No hay archivos
        </li>
    </ul>
</div>
</script>
