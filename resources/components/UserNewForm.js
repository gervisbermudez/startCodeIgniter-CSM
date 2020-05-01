var UserNewForm = new Vue({
  el: "#root",
  data: {
    loader: true,
    editMode: false,
    user_id: "",
    usergroups: [],
    form: new VueForm({
      username: { value: null, required: true, type: 'username', maxLength: 15, minLength: 5 },
      password: { value: null, required: true, type: 'password', maxLength: 25, minLength: 5 },
      email: { value: '', required: true, type: 'email', maxLength: 150 },
      usergroup_id: { value: '', required: true, type: 'number' },
      nombre: { value: '', required: true, type: 'name', maxLength: 15, minLength: 3 },
      apellido: { value: '', required: true, type: 'name', maxLength: 15, minLength: 3 },
      telefono: { value: '', required: true, type: 'number', maxLength: 12 },
      direccion: { value: '', required: true, type: 'alphanumeric', maxLength: 60 },
    }),
    status: true,
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
    validateField(field) {
      let self = UserNewForm;
      if (self.form.validateField(field)) {
        self.serverValidation(field);
        return;
      }
      return self.form.fields[field].valid;
    },
    save() {
      var self = this;
      self.form.validate();
      if (self.form.errors.length == 0) {
        this.loader = true;
        $.ajax({
          type: "POST",
          url: BASEURL + "api/v1/users/",
          data: self.getUserData(),
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
      } else {
        M.toast({ html: 'Verifique todos los campos del formulario' });
      }
    },
    getUserData() {
      let form = this.form.fields;
      return {
        user_id: this.user_id,
        username: form.username.value,
        password: form.password.value,
        email: form.email.value,
        usergroup_id: form.usergroup_id.value,
        user_data: {
          nombre: form.nombre.value,
          apellido: form.apellido.value,
          direccion: form.direccion.value,
          telefono: form.telefono.value,
        }
      }
    },
    serverValidation(field) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/usuarios/ajax_check_field",
        data: {
          field: field,
          value: self.form.fields[field].value
        },
        dataType: "json",
        success: function (response) {
          if (response.code) {
            self.form.fields[field].valid = response.data;
            if (response.data) {
              self.form.markFieldAsValid(field);
            }else {
              self.form.fields[field].errorText = "The " + field + " is already registered";
            }
            self.$forceUpdate();
          }
        }
      });
    },
    getUsergroups() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/users/usergroups",
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
          type: "GET",
          url: BASEURL + "api/v1/users/" + user_id,
          data: {},
          dataType: "json",
          success: function (response) {
            if (response.code == 200) {
              let data = response.data[0];
              self.user_id = data.user_id;
              self.form.fields.username.value = data.username;
              self.form.fields.email.value = data.email;
              self.status = data.status;
              self.form.fields.usergroup_id.value = data.usergroup_id;
              self.form.fields.nombre.value = data.user_data.nombre;
              self.form.fields.apellido.value = data.user_data.apellido;
              self.form.fields.direccion.value = data.user_data.direccion;
              self.form.fields.telefono.value = data.user_data.telefono;
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
