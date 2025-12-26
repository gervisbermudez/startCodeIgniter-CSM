var VideosNewForm = new Vue({
  el: "#form",
  data: {
    debug: typeof DEBUGMODE !== 'undefined' ? DEBUGMODE : false,
    loader: false,
    description: '',
    preview: '',
  },
  methods: {
    initPlugins() {
      tinymce.init({
        base_url: BASEURL + '/public/vendors/tinymce/js/tinymce',
        selector: "#id_cazary",
        plugins: ["link", "table", "code"],
        setup: (editor) => {
          editor.on("Change", (e) => {
            this.debug ? console.log("tinymce change fired") : null;
            this.description = tinymce.editors["id_cazary"].getContent();
          });
        },
      });
    },
    onSelectImageCallcack(selectedFiles) {
      if (selectedFiles && selectedFiles.length > 0) {
        try {
          // Normalize to ExplorerFile instance like other forms
          const fileObj = new ExplorerFile(selectedFiles[0]);
          this.preview =
            (typeof fileObj.get_full_file_path === "function" && fileObj.get_full_file_path()) ||
            fileObj.full_path ||
            fileObj.url ||
            "";
        } catch (e) {
          // Fallback if ExplorerFile is not available
          this.preview = selectedFiles[0].full_path || selectedFiles[0].url || "";
        }
        // Actualiza el input de imagen en el DOM
        const imgInput = document.getElementById("imagen");
        if (imgInput) imgInput.value = this.preview;
      }
      // Cierra el modal
        try {
          let modalEl = document.getElementById('fileUploader');
          if (modalEl && typeof M !== 'undefined' && M.Modal) {
            let instance = M.Modal.getInstance(modalEl);
            if (instance && instance.close) instance.close();
          }
        } catch (e) {
          console.warn('Error closing fileUploader modal', e);
        }
      // Reinitialize Materialbox and tooltips for the newly added image
      setTimeout(() => {
        var elems = document.querySelectorAll('.materialboxed');
        M.Materialbox.init(elems, {});
      }, 300);
    },
    removePreview() {
      this.preview = '';
      const el = document.getElementById('imagen');
      if (el) el.value = '';
    },
    getData() {
      return {
        id: document.querySelector('input[name="id"]') ? document.querySelector('input[name="id"]').value : '',
        nombre: document.getElementById('nombre') ? document.getElementById('nombre').value : '',
        description: this.description || '',
        duration: document.getElementById('duracion') ? document.getElementById('duracion').value : '',
        youtubeid: document.getElementById('youtubeid') ? document.getElementById('youtubeid').value : '',
        preview: this.preview || '',
        paypal: document.getElementById('paypal') ? document.getElementById('paypal').value : '',
        categorias: Array.from(document.querySelectorAll('select[name="categorias[]"] option:checked')).map(o => o.value),
        status: document.querySelector('input[name="status"]') && document.querySelector('input[name="status"]').checked ? '1' : '0'
      };
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + 'api/v1/videos';
      this.loader = true;
      $.ajax({
        type: 'POST',
        url: url,
        data: self.getData(),
        dataType: 'json',
        success: function(response) {
          self.debug ? console.log(url, response) : null;
          self.loader = false;
          if (response.code == 200) {
            // API returns { video_id: <id> }
            var videoId = response.data && (response.data.video_id || response.data.videoId || response.data.id);
            if (typeof callBack == 'function') callBack(response);
            if (videoId) {
              window.location.href = BASEURL + 'admin/videos/ver/' + videoId;
            } else if (response.data && response.data.redirect) {
              window.location.href = response.data.redirect;
            }
          } else {
            M.toast({ html: response.error_message || 'Error saving' });
          }
        },
        error: function(err) {
          self.loader = false;
          self.debug ? console.error(err) : null;
          M.toast({ html: 'Ocurrió un error inesperado' });
        }
      });
    },
  },
  mounted: function () {
    this.$nextTick(() => {
      setTimeout(() => {
        this.initPlugins();
      }, 1000);
      // init materialbox for any server-rendered preview image
      setTimeout(() => {
        var elems = document.querySelectorAll('.materialboxed');
        M.Materialbox.init(elems, {});
      }, 1200);
      // Intercept native form submit and send via AJAX
      const formEl = document.getElementById('form');
      if (formEl) {
        formEl.addEventListener('submit', (e) => {
          e.preventDefault();
          this.runSaveData();
        });
      }
    });
  },
});
