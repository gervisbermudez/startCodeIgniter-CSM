var UserNewForm = new Vue({
  el: "#root",
  mixins: [mixins],
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    user_id: "",
    usergroups: [],
    avatar: "",
    showPassword: false,
    form: new VueForm({
      username: {
        value: "",
        required: true,
        type: "alphanumeric",
        maxLength: 18,
        minLength: 5,
      },
      password: {
        value: "",
        required: true,
        type: "password",
        maxLength: 25,
        minLength: 8,
      },
      email: { value: "", required: true, type: "email", maxLength: 150 },
      usergroup_id: { value: "", required: true, type: "number" },
      nombre: { value: "", required: false, type: "name", maxLength: 40 },
      apellido: { value: "", required: false, type: "name", maxLength: 40 },
      cargo: { value: "", required: false, type: "text", maxLength: 80 },
      telefono: { value: "", required: false, type: "phone", maxLength: 18 },
      direccion: { value: "", required: false, type: "address", maxLength: 60 },
      bio: { value: "", required: false, type: "text", maxLength: 400 },
    }),
  },
  computed: {
    canSave: function () {
      var fields = this.form.fields;
      if (!fields.username.value || !fields.email.value || !fields.usergroup_id.value) {
        return false;
      }
      if (!this.editMode && !fields.password.value) {
        return false;
      }
      return true;
    },
    avatarUrl: function () {
      if (!this.avatar) {
        return "";
      }
      var path = String(this.avatar).replace(/^\.\//, "");
      if (path.indexOf("http") === 0) {
        return path;
      }
      return BASEURL + path;
    },
    avatarDir: function () {
      var name = this.form.fields.username.value;
      if (name) {
        return "./public/img/profile/" + name + "/";
      }
      return "./uploads/";
    },
    changePasswordUrl: function () {
      return BASEURL + "admin/users/changePassword/" + this.user_id;
    },
    profileUrl: function () {
      return BASEURL + "admin/users/ver/" + this.user_id;
    },
  },
  watch: {
    usergroups: function () {
      if (!this.loader) {
        this.$nextTick(this.initUsergroupSelect);
      }
    },
    loader: function (isLoading) {
      if (!isLoading) {
        this.$nextTick(this.initUsergroupSelect);
      }
    },
  },
  methods: {
    fieldValue: function (field) {
      var value = this.form.fields[field].value;
      if (value === null || value === undefined) {
        return "";
      }
      return String(value).trim();
    },
    validateField: function (field) {
      if (this.form.validateField(field)) {
        this.serverValidation(field);
        return true;
      }
      return this.form.fields[field].valid;
    },
    validateOptionalField: function (field) {
      if (!this.fieldValue(field)) {
        this.form.fields[field].touched = true;
        this.form.markFieldAsValid(field);
        this.form.fields[field].errorText = "";
        this.$forceUpdate();
        return true;
      }
      return this.form.validateField(field);
    },
    save: function () {
      var self = this;
      self.form.validate();
      ["nombre", "apellido", "cargo", "telefono", "direccion", "bio"].forEach(function (field) {
        self.validateOptionalField(field);
      });
      if (self.form.errors.length !== 0) {
        self.toast("toast_form_invalid");
        return;
      }
      self.loader = true;
      self.confirmUniqueThenPost();
    },
    confirmUniqueThenPost: function () {
      var self = this;
      var fields = ["username", "email"];
      var pending = fields.length;
      var blocked = false;
      var finish = function () {
        pending--;
        if (pending > 0) {
          return;
        }
        if (blocked) {
          self.toast("toast_form_invalid");
          self.loader = false;
          self.$forceUpdate();
          return;
        }
        self.postUser();
      };
      fields.forEach(function (field) {
        $.ajax({
          type: "POST",
          url: BASEURL + "admin/users/ajax_check_field",
          data: {
            field: field,
            value: self.form.fields[field].value,
            user_id: self.user_id || "",
          },
          dataType: "json",
          success: function (response) {
            if (response.code && !response.data) {
              blocked = true;
              self.form.markFieldAsInvalid(field);
              self.form.fields[field].errorText = self.lang("users_form_field_taken");
            }
          },
          complete: finish,
        });
      });
    },
    postUser: function () {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/users/",
        data: self.getUserData(),
        dataType: "json",
        success: function (response) {
          if (self.debug) {
            console.log(response);
          }
          if (response.code == 200) {
            window.location =
              BASEURL + "admin/users/ver/" + response.data.user_id;
            return;
          }
          self.toastError(null, response);
          self.loader = false;
        },
        error: function (xhr) {
          self.toastError(xhr);
          self.loader = false;
        },
      });
    },
    getUserData: function () {
      var form = this.form.fields;
      var payload = {
        username: form.username.value,
        email: form.email.value,
        usergroup_id: form.usergroup_id.value,
        user_data: {
          nombre: this.fieldValue("nombre"),
          apellido: this.fieldValue("apellido"),
          cargo: this.fieldValue("cargo"),
          direccion: this.fieldValue("direccion"),
          telefono: this.fieldValue("telefono"),
          bio: this.fieldValue("bio"),
          avatar: this.avatar || "",
        },
      };
      if (this.user_id) {
        payload.user_id = this.user_id;
      }
      if (!this.editMode || form.password.value) {
        payload.password = form.password.value;
      }
      return payload;
    },
    serverValidation: function (field) {
      var self = this;
      if (field !== "username" && field !== "email") {
        return;
      }
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/users/ajax_check_field",
        data: {
          field: field,
          value: self.form.fields[field].value,
          user_id: self.user_id || "",
        },
        dataType: "json",
        success: function (response) {
          if (self.debug) {
            console.log(response);
          }
          if (response.code) {
            if (response.data) {
              self.form.markFieldAsValid(field);
              self.form.fields[field].errorText = "";
            } else {
              self.form.markFieldAsInvalid(field);
              self.form.fields[field].errorText = self.lang("users_form_field_taken");
            }
            self.$forceUpdate();
          }
        },
      });
    },
    getUsergroups: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/users/usergroups",
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.usergroups = response.data || [];
            if (!user_id) {
              self.loader = false;
              self.$nextTick(function () {
                self.initUsergroupSelect();
              });
            }
            return;
          }
          self.toastError(null, response);
          if (!user_id) {
            self.loader = false;
          }
        },
        error: function (xhr) {
          self.toastError(xhr);
          if (!user_id) {
            self.loader = false;
          }
        },
      });
    },
    initUsergroupSelect: function () {
      var el = document.getElementById("usergroup_id");
      if (el && window.M && M.FormSelect) {
        var prev = M.FormSelect.getInstance(el);
        if (prev) {
          prev.destroy();
        }
        M.FormSelect.init(el, {});
      }
      if (window.M && M.updateTextFields) {
        M.updateTextFields();
      }
      var bio = document.getElementById("bio");
      if (bio && window.M && M.textareaAutoResize) {
        M.textareaAutoResize(bio);
      }
    },
    checkEditMode: function () {
      if (!user_id) {
        return;
      }
      var self = this;
      self.editMode = true;
      self.form.fields.password.required = false;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/users/" + user_id,
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            var data = response.data;
            var userData = data.user_data || {};
            self.user_id = data.user_id;
            self.form.fields.username.value = data.username || "";
            self.form.fields.email.value = data.email || "";
            self.form.fields.usergroup_id.value = data.usergroup_id || "";
            self.form.fields.nombre.value = userData.nombre || "";
            self.form.fields.apellido.value = userData.apellido || "";
            self.form.fields.cargo.value = userData.cargo || "";
            self.form.fields.direccion.value = userData.direccion || "";
            self.form.fields.telefono.value = userData.telefono || "";
            self.form.fields.bio.value = userData.bio || "";
            self.avatar = userData.avatar || "";
          }
          self.loader = false;
          self.$nextTick(function () {
            self.initUsergroupSelect();
          });
        },
        error: function (xhr) {
          self.toastError(xhr);
          self.loader = false;
        },
      });
    },
    generatePassword: function () {
      var upper = "ABCDEFGHJKLMNPQRSTUVWXYZ";
      var lower = "abcdefghijkmnopqrstuvwxyz";
      var nums = "23456789";
      var special = "#.?!@$%^*-_";
      var all = upper + lower + nums + special;
      var pick = function (set) {
        return set.charAt(Math.floor(Math.random() * set.length));
      };
      var chars = [pick(upper), pick(lower), pick(nums), pick(special)];
      var i;
      for (i = 0; i < 8; i++) {
        chars.push(pick(all));
      }
      for (i = chars.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = chars[i];
        chars[i] = chars[j];
        chars[j] = tmp;
      }
      this.form.fields.password.value = chars.join("");
      this.showPassword = true;
      this.form.validateField("password");
      this.$nextTick(function () {
        if (window.M && M.updateTextFields) {
          M.updateTextFields();
        }
      });
      this.toast("users_form_password_generated");
    },
    onPickAvatar: function (selectedFiles) {
      if (!selectedFiles || !selectedFiles.length) {
        return;
      }
      var file = new ExplorerFile(selectedFiles[0]);
      this.avatar = file.get_relative_file_path();
      var modalEl = document.getElementById("folderSelector");
      var instance = modalEl && window.M && M.Modal.getInstance(modalEl);
      if (instance) {
        instance.close();
      }
    },
    clearAvatar: function () {
      this.avatar = "";
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUsergroups();
      this.checkEditMode();
    });
  },
});
