var loginForm = new Vue({
  el: "#root",
  data: {
    loader: true,
    editMode: false,
    user_id: "",
    username: "",
    password: "",
    email: "",
    usergroup_id: "",
    usergroups: [],
    user_data: {
      nombre: "",
      apellido: "",
      direccion: "",
      telefono: ""
    },
    status: true,
    validFields: {
      username: false,
      email: false,
      nombre: false,
      apellido: false,
      direccion: false,
      telefono: false
    },
    patterns: {
      letters: /^[A-Za-z]+$/,
      email: /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
      numbers: /^\d+$/,
      alphanumeric: /^[ a-z0-9A-ZÀ-ÿ\u00f1\u00d1]*$/g
    }
  },
  computed: {
    btnEnable: function () {
      let result = true;
      for (const key in this.validFields) {
        if (this.validFields.hasOwnProperty(key)) {
          const element = this.validFields[key];
          if (!element) {
            result = false;
          }
        }
      }
      return result;
    }
  },
  methods: {
    save() {
      var self = this;
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/usuarios/ajax_save_user",
        data: {
          username: self.username,
          password: self.password,
          email: self.email,
          usergroup_id: self.usergroup_id,
          user_data: self.user_data
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            console.log(response);
            window.location = BASEURL + 'admin/usuarios/ver/' + response.data.user_id;
          } else {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          }
        },
        error: function (response) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        }
      });
    },
    validateField(field) {
      switch (field) {
        case "username":
          if (!this.username.match(this.patterns.letters)) {
            this.validFields[field] = false;
            M.toast({ html: "Only letters are allowed" });
            return;
          } else {
            this.serverValidation(field);
          }
          break;
        case "email":
          if (!this.email.match(this.patterns.email)) {
            this.validFields[field] = false;
            M.toast({ html: "Email invalid" });
            return;
          } else {
            this.serverValidation(field);
          }
          break;
        case "nombre":
          if (!this.user_data.nombre.match(this.patterns.letters)) {
            this.validFields.nombre = false;
            M.toast({ html: "Only letters are allowed" });
            return;
          }
          break;
        case "apellido":
          if (!this.user_data.apellido.match(this.patterns.letters)) {
            this.validFields.apellido = false;
            M.toast({ html: "Only letters are allowed" });
            return;
          }
          break;
        case "telefono":
          if (!this.user_data.telefono.match(this.patterns.numbers)) {
            this.validFields.telefono = false;
            M.toast({ html: "Only numbers are allowed" });
            return;
          }
          break;
        case "direccion":
          if (!this.user_data.direccion.match(this.patterns.alphanumeric)) {
            this.validFields.direccion = false;
            M.toast({ html: "Simbols are not allowed" });
            return;
          }
          break;
      }

      this.validFields[field] = true;

    },
    serverValidation(field) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/usuarios/ajax_check_field",
        data: {
          field: field,
          value: self[field]
        },
        dataType: "json",
        success: function (response) {
          if (response.code) {
            self.validFields[field] = response.data;
            if (!response.data) {
              M.toast({ html: "The " + field + " is already registered!" });
            }
            self.$forceUpdate();
          }
        }
      });
    },
    getUsergroups() {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/usuarios/ajax_get_usergroups",
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.usergroups = response.data;
            setTimeout(() => {
              var elems = document.querySelectorAll("select");
              var instances = M.FormSelect.init(elems, {});
            }, 2000);
            self.loader = false;
          }
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        }
      });
    },
    checkEditMode() {
      if (user_id) {
        var self = this;
        self.editMode = true;
        $.ajax({
          type: "POST",
          url: BASEURL + "admin/usuarios/ajax_get_users",
          data: {
            user_id: user_id
          },
          dataType: "json",
          success: function (response) {
            if (response.code == 200) {
              let data = response.data[0];
              self.user_id = data.user_id;
              self.username = data.username;
              self.email = data.email;
              self.status = data.status;
              self.usergroup_id = data.usergroup_id;
              self.user_data.nombre = data.user_data.nombre;
              self.user_data.apellido = data.user_data.apellido;
              self.user_data.direccion = data.user_data.direccion;
              self.user_data.telefono = data.user_data.telefono;
            }
            self.loader = false;
            setTimeout(() => {
              M.updateTextFields();
            }, 1000);
          },
          error: function (error) {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          }
        });
      }
    }
  },
  mounted: function () {
    this.$nextTick(function () {
      console.log("mounted UserNewForm");
      this.getUsergroups();
      this.checkEditMode();
    });
  }
});
