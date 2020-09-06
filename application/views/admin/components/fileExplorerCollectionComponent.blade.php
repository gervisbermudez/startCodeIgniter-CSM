<script type="text/x-template" id="fileExplorerCollection-template">
    <ul class="collection" v-if="files.length">
    <li class="collection-header collection-item avatar">
        <h5>Tus Archivos Favoritos</h5>
    </li>
      <li class="collection-item avatar" v-for="(file, index) in files" :key="index">
          <i class="material-icons circle red">insert_drive_file</i>
          <a :href="file.getFullFilePath()" class="title">@{{file.get_filename()}}</a>
          <p> 
          <a :href="file.getFullSharePath()" class="title">Share File</a>
          <br>
              @{{file.date_create}}
          </p>
          <a href="#!" class="secondary-content" v-on:click="featuredFileServe(file);"><i class="material-icons">grade</i></a>
      </li>
  </ul>
</script>
