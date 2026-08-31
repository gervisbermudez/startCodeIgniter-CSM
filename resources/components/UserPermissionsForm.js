var UserPermissionsForm = new Vue({
  el: "#root",
  mixins: [mixins],
  data: {
    loader: true,
    editMode: false,
    usergroup_id: null,
    name: "",
    description: "",
    level: null,
    status: true,
    date_create: "",
    date_update: "",
    permissions: [],
    user_id: null,
    parent_id: null,
    usergroup_permisions: [],
  },
  computed: {
    btnEnable: function () {
      return !!this.name && !!this.description;
    },
    permissionModules: function () {
      var groups = {};
      var order = [];
      this.permissions.forEach(function (permission) {
        var mod = permission.module || "other";
        if (!groups[mod]) {
          groups[mod] = [];
          order.push(mod);
        }
        groups[mod].push(permission);
      });
      return order.map(function (mod) {
        return { module: mod, items: groups[mod] };
      });
    },
  },
  methods: {
    moduleTitle: function (mod) {
      var key = "perm_module_" + mod;
      var translated = this.lang(key);
      if (translated && translated !== key) {
        return translated;
      }
      return String(mod || "").replace(/_/g, " ");
    },
    editorHas: function (name) {
      var list = typeof editorPermisions !== "undefined" && editorPermisions ? editorPermisions : [];
      if (!Array.isArray(list)) {
        list = Object.keys(list).map(function (k) {
          return list[k];
        });
      }
      return list.indexOf(name) !== -1;
    },
    moduleHasEditable: function (mod) {
      return this.permissions.some(function (permission) {
        return permission.module === mod && !permission.locked;
      });
    },
    moduleAllChecked: function (mod) {
      var editable = this.permissions.filter(function (permission) {
        return permission.module === mod && !permission.locked;
      });
      if (!editable.length) {
        return false;
      }
      return editable.every(function (permission) {
        return permission.enabled;
      });
    },
    toggleModule: function (mod, checked) {
      this.permissions.forEach(function (permission) {
        if (permission.module === mod && !permission.locked) {
          permission.enabled = !!checked;
        }
      });
    },
    save: function () {
      var self = this;
      if (!this.btnEnable) {
        return;
      }
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/users/usergroups",
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            var createdId = response.data && response.data.usergroup_id;
            if (!self.editMode && createdId) {
              self.toast("toast_saved");
              window.location.href = BASEURL + "admin/users/editGroup/" + createdId;
              return;
            }
            self.editMode = true;
            self.usergroup_id = createdId || self.usergroup_id;
            self.loader = false;
            self.toast("toast_saved");
            self.initPlugins();
            return;
          }
          self.loader = false;
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
        },
      });
    },
    getData: function () {
      var payload = {
        name: this.name || "",
        description: this.description || "",
        status: this.status ? 1 : 2,
        permissions: this.permissions
          .filter(function (permission) {
            return permission.enabled && !permission.locked;
          })
          .map(function (permission) {
            return { permisions_id: permission.permisions_id };
          }),
      };
      if (this.usergroup_id) {
        payload.usergroup_id = this.usergroup_id;
      }
      if (this.level) {
        payload.level = this.level;
      }
      return payload;
    },
    applyCatalog: function (rows) {
      var self = this;
      var grants = this.usergroup_permisions || [];
      this.permissions = (rows || []).map(function (row) {
        var name = row.permision_name;
        return {
          permisions_id: row.permisions_id,
          permision_name: name,
          label: row.label || name,
          module: row.module || "",
          enabled: grants.indexOf(name) !== -1,
          locked: !self.editorHas(name),
        };
      });
    },
    getCatalog: function () {
      var self = this;
      fetch(BASEURL + "api/v1/users/allpermissions")
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          if (response && response.code == 200) {
            self.applyCatalog(response.data);
          }
          if (self.editMode) {
            return;
          }
          self.loader = false;
          self.initPlugins();
        })
        .catch(function (err) {
          self.loader = false;
          self.toast("usergroups_unexpected_error");
          console.error(err);
        });
    },
    loadGroup: function () {
      var self = this;
      fetch(BASEURL + "api/v1/users/usergroups/" + usergroup_id)
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          if (response && response.code == 200 && response.data) {
            var data = response.data;
            self.usergroup_id = data.usergroup_id;
            self.name = data.name;
            self.description = data.description;
            self.level = data.level;
            self.date_create = data.date_create;
            self.date_update = data.date_update;
            self.status = data.status == 1 || data.status == "1";
            self.user_id = data.user_id;
            self.parent_id = data.parent_id;
            self.usergroup_permisions = data.usergroup_permisions || [];
            self.applyCatalog(self.permissions.length ? self.permissions.map(function (p) {
              return {
                permisions_id: p.permisions_id,
                permision_name: p.permision_name,
                label: p.label,
                module: p.module,
              };
            }) : []);
          }
          self.loader = false;
          self.$nextTick(function () {
            M.updateTextFields();
            self.initPlugins();
          });
        })
        .catch(function (err) {
          self.loader = false;
          self.toast("usergroups_unexpected_error");
          console.error(err);
        });
    },
    checkEditMode: function () {
      var self = this;
      if (usergroup_id && editMode == "edit") {
        this.editMode = true;
        fetch(BASEURL + "api/v1/users/allpermissions")
          .then(function (response) {
            return response.json();
          })
          .then(function (response) {
            var rows = response && response.code == 200 ? response.data : [];
            return fetch(BASEURL + "api/v1/users/usergroups/" + usergroup_id).then(function (groupRes) {
              return groupRes.json().then(function (groupJson) {
                return { rows: rows, groupJson: groupJson };
              });
            });
          })
          .then(function (bundle) {
            if (bundle.groupJson && bundle.groupJson.code == 200 && bundle.groupJson.data) {
              var data = bundle.groupJson.data;
              self.usergroup_id = data.usergroup_id;
              self.name = data.name;
              self.description = data.description;
              self.level = data.level;
              self.date_create = data.date_create;
              self.date_update = data.date_update;
              self.status = data.status == 1 || data.status == "1";
              self.user_id = data.user_id;
              self.parent_id = data.parent_id;
              self.usergroup_permisions = data.usergroup_permisions || [];
            }
            self.applyCatalog(bundle.rows);
            self.loader = false;
            self.$nextTick(function () {
              M.updateTextFields();
              self.initPlugins();
            });
          })
          .catch(function (err) {
            self.loader = false;
            self.toast("usergroups_unexpected_error");
            console.error(err);
          });
        return;
      }
      this.getCatalog();
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.checkEditMode();
    });
  },
});
