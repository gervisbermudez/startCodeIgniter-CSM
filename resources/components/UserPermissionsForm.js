var UserPermissionsForm = new Vue({
  el: "#root",
  data: {
    loader: true,
    editMode: false,
    usergroup_id: null,
    name: "New User Group",
    description: "Describe the user group",
    level: null,
    status: false,
    date_publish: "",
    date_create: "",
    date_update: "",
    permissions: [],
    user_id: null,
    parent_id: null,
    user: null,
    data: 1,
    usergroup_permisions: [],
    usergroups: [],
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      let enable = this.name ? true : false;
      return enable;
    },
    checkedPermissions: function () {
      return this.permissions.filter((permission) => {
        return permission.enabled;
      });
    }
  },
  methods: {
    autoSave() {
      if (!this.status) {
        this.runSaveData();
        this.debug ? console.log("running autosave...") : null;
      }
    },
    save() {
      var callBack = (response) => {
        var toastHTML = "<span>Group saved </span>";
        M.toast({ html: toastHTML });
      };
      this.loader = true;
      this.runSaveData(callBack);
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/users/usergroups";
      $.ajax({
        type: "POST",
        url: url,
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          setTimeout(() => {
            self.loader = false;
          }, 1500);
          if (response.code == 200) {
            self.editMode = true;
            self.usergroup_id = response.data.usergroup_id;
            if (typeof callBack == "function") {
              callBack(response);
            }
          } else {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          }
        },
        error: function (response) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    isChecked(permission) {
      return this.usergroup_permisions.includes(permission);
    },
    getData: function () {
      return {
        usergroup_id: this.usergroup_id || "",
        name: this.name || "",
        level: this.level || "",
        description: this.description || "",
        status: this.status ? 1 : 0,
        parent_id: this.parent_id,
        permissions: this.checkedPermissions
      };
    },
    getUserGroups: function () {
      var self = this;
      var url = BASEURL + "api/v1/users/usergroups/";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          let usergroups = response.data;
          usergroups.map((element) => {
            if (element.user) {
              element.user = new User(element.user);
            } else {
              element.user = new User({});
            }
            return element;
          });
          self.usergroups = usergroups;
          self.loader = false;
          this.initPlugins();
        })
        .catch((response) => {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        });
    },
    getPermissions() {
      var url = BASEURL + "api/v1/users/permissions/";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          this.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            this.permissions = response.data.map((permission, index) => {
              return {
                ...permission.permission,
                enabled: this.usergroup_permisions.includes(
                  permission.permission.permision_name
                ),
              };
            });
          }
        })
        .catch((err) => {
          this.debug ? console.log(err) : null;
          this.loader = false;
        });
    },
    checkEditMode() {
      if (usergroup_id && editMode == "edit") {
        this.editMode = true;
        var url = BASEURL + "api/v1/users/usergroups/" + usergroup_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            this.loader = false;
            this.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              let data = response.data;
              this.usergroup_id = data.usergroup_id;
              this.name = data.name;
              this.description = data.description;
              this.level = data.level;
              this.date_create = data.date_create;
              this.date_update = data.date_update;
              this.status = data.status;
              this.user_id = data.user_id;
              this.level = data.level;
              this.usergroup_permisions = data.usergroup_permisions;
              this.parent_id = data.parent_id;
              this.user = new User(data.user);
            }
            this.initPlugins();
            this.getPermissions();
          })
          .catch((response) => {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          });
      } else {
        this.getPermissions();
        this.loader = false;
      }
    },
    initPlugins() {
      setTimeout(() => {
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
      }, 3000);
    },
    setRealOrder() {
      var data = this.group.sortable("serialize").get();
      var strData = JSON.stringify(data);
      var strData = strData.replace(/children/g, "subitems");
      data = JSON.parse(strData);
      if (data) {
        let result = this.getMenuItemsData(data[0]);
        this.realOrder = result;
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted UserPermissionsForm") : null;
      this.checkEditMode();
    });
  },
});
