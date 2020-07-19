var userProfile = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    user: {
      user_id: null,
    },
  },
  watch: {
    "user.user_id": function (value) {
      if ($("#input-100").data("fileinput")) {
        $("#input-100").data("fileinput").uploadExtraData.curDir =
          "./public/img/profile/" + this.user.username + "/";
      }
    },
  },
  methods: {
    getUser() {
      var self = this;
      self.loader = true;
      self.users = [];
      var user_id = window.location.pathname.split("/")[4];
      if (user_id) {        
        var url = BASEURL + "api/v1/users/" + user_id;
        $.ajax({
          type: "GET",
          url: url,
          data: {},
          dataType: "json",
          success: function (response) {
            self.debug ? console.log(url, response) : null;
            self.loader = false;
            self.user = response.data;
          },
          error: function (error) {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          },
        });
      }
    },
    updateAvatar() {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/users/avatar",
        data: {
          avatar: self.user.user_data.avatar,
          user_id: self.user.user_id,
        },
        dataType: "json",
        success: function (response) {
          self.loader = false;
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    getAvatarUrl(avatar) {
      return this.base_url(
        "public/img/profile/" + this.user.username + "/" + avatar
      );
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        var instances = M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUser();
      this.initPlugins();
      window.uploadCallback = (event, previewId, index, fileId) => {
        console.log(fileId);
        this.user.user_data.avatar = fileId.substring(fileId.indexOf("_") + 1);
        this.updateAvatar();
        M.toast({ html: "File Uploaded!" });
        let instance = M.Modal.getInstance($("#modal1"));
        instance.close();
      };
      fileUploaderModule.multiple = false;
      fileUploaderModule.callBakSelectedImagen = (selectedFiles) => {
        console.log(selectedFiles);
        let file = selectedFiles[0];
        userProfile.user.user_data.avatar = file['file_name'] + '.' + file['file_type'];
        fileUploaderModule.copyFileTo(selectedFiles[0], './public/img/profile/' + this.user.username + '/', function (response) {
          if (response.code == 200) {
              userProfile.updateAvatar();
              M.toast({ html: "<span>Done! </span>" });
            }
        });
      }
    });
  },
});
