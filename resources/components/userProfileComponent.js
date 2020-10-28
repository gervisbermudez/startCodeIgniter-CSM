Vue.component("formCustom", {
  template: "#form-custom-template",
  props: ["form"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {},
  mounted: function () {
    this.$nextTick(function () {});
  },
});

Vue.component("pageCard", {
  template: "#page-card-template",
  props: ["page"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {
    getcontentText: function (page) {
      var span = document.createElement("span");
      span.innerHTML = page.content;
      let text = span.textContent || span.innerText;
      return text.substring(0, 220) + "...";
    },
    getPageImagePath() {
      if (this.imagen_file) {
        return (
          BASEURL +
          this.imagen_file.file_path.substr(2) +
          this.imagen_file.file_name +
          "." +
          this.imagen_file.file_type
        );
      }
      return BASEURL + "public/img/default.jpg";
    },
    getPageFullPath: function (page) {
      if (page.status == 1) {
        return BASEURL + page.path;
      }
      return BASEURL + "admin/paginas/editar/" + page.page_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});

var userProfile = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    user: new User(),
    timelineData: [],
  },
  mixins: [mixins],
  watch: {
    "user.user_id": function (value) {
      if ($("#input-100").data("fileinput")) {
        $("#input-100").data("fileinput").uploadExtraData.curDir =
          "./public/img/profile/" + this.user.username + "/";
      }
    },
  },
  filters: {
    shortDate: function (value) {
      return value.split("at")[0];
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
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.debug ? console.log(url, response) : null;
            self.loader = false;
            self.user = new User(response.data);
          })
          .catch((response) => {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          });
      }
    },
    getUserTimeline() {
      var self = this;
      self.timelineData = [];
      var user_id = window.location.pathname.split("/")[4];
      if (user_id) {
        var url = BASEURL + "/api/v1/users/timeline/" + user_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.debug ? console.log(url, response) : null;
            self.loader = false;
            let timelineData = response.data.map((item, index) => {
              switch (item.model_type) {
                case "page":
                  return new Page(item);
              }
              return item;
            });
            let timelineGroups = {};
            timelineData.forEach((element) => {
              let date = element.date_create.split(" ")[0] + " 00:00:00";
              if (timelineGroups[date] == undefined) {
                timelineGroups[date] = [];
              }
              timelineGroups[date].push(element);
            });
            self.timelineData = timelineGroups;
          })
          .catch((error) => {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
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
      return this.user.get_avatarurl();
    },
    initPlugins: function () {
      M.Tabs.init(document.getElementById("user-tabs"), {});
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 4000);
    },
    uploadCallback: function (seletedFiles) {
      let instance = M.Modal.getInstance($(".modal"));
      instance.close();
      console.log(seletedFiles);
      let file = new ExplorerFile(seletedFiles[0]);
      this.user.user_data.avatar = file.get_relative_file_path();
      this.updateAvatar();
    }
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUser();
      this.getUserTimeline();
      this.initPlugins();
    });
  },
});
