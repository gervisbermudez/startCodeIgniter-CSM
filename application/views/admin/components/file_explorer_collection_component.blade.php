<script type="text/x-template" id="fileExplorerCollection-template">
<div class="panel fileExplorerCollection-root">
    <div class="title">
        <h5><a href="{{base_url('admin/files')}}">Latest Files</a></h5>
        <div class="subtitle sub-header">
            @{{files.length}} Files
        </div>
        <img src="{{base_url()}}public/img/admin/dashboard/undraw_folder_files_nweq.png" />
    </div>
    <ul class="collection">
        <li  v-if="files.length" class="collection-item avatar" v-for="(file, index) in shortFiles" :key="index">
            <i class="material-icons circle red">insert_drive_file</i>
            <a :href="file.get_full_file_path()" class="item-title">@{{file.get_filename()}}</a>
            <p>
                @{{timeAgo(file.date_create)}}
                <br>
            <a :href="file.get_full_share_path()" class="item-title">Share File</a>
            </p>
            <a href="#!" class="secondary-content" :class="{'yellow-text': file.featured == '1'}" v-on:click="featuredFileServe(file);"><i class="material-icons">grade</i></a>
        </li>
        <li v-else class="collection-item center-align" style="padding: 40px 20px;">
            <i class="material-icons" style="font-size: 48px; color: #9e9e9e;">folder_open</i>
            <p style="color: #9e9e9e; margin-top: 10px;">No files uploaded yet</p>
            <a href="{{base_url('admin/files')}}" class="btn-small waves-effect waves-light" style="margin-top: 10px;">Upload Files</a>
        </li>
    </ul>
</div>
</script>
