var changePassword = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    user_id: "",
    user: {},
    form: new VueForm({
      currentPassword: {
        value: null,
        required: true,
        type: "password",
        maxLength: 25,
        minLength: 5,
      },
      password: {
        value: null,
        required: true,
        type: "password",
        maxLength: 25,
        minLength: 5,
      },
      password2: {
        value: null,
        required: true,
        type: "password",
        maxLength: 25,
        minLength: 5,
      },
    }),
    status: true,
  },
  computed: {
    btnEnable: function () {
      this.form.validate();
      return this.form.errors.length == 0 && this.samePassword;
    },
    samePassword() {
      return (
        this.form.fields.password.value == this.form.fields.password2.value
      );
    },
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
        var url = BASEURL + "api/v1/users/changePassword";
        $.ajax({
          type: "POST",
          url: url,
          data: self.getUserData(),
          dataType: "json",
          success: function (response) {
            self.debug ? console.log(url, response) : null;
            /*  window.location =
              BASEURL + "admin/usuarios/ver/" + response.data.user_id; */
            M.toast({ html: response.error_message });
            self.loader = false;
          },
          error: function (response) {
            self.loader = false;
            M.toast({ html: "Ocurrió un error inesperado" });
            console.error(error);
          },
        });
      } else {
        M.toast({ html: "Verifique todos los campos del formulario" });
      }
    },
    getUserData() {
      let form = this.form.fields;
      return {
        user_id: user_id,
        password: form.password.value,
        currentPassword: form.currentPassword.value,
      };
    },
    checkEditMode() {
      if (user_id) {
        var self = this;
        self.editMode = true;
        var url = BASEURL + "api/v1/users/" + user_id;
        $.ajax({
          type: "GET",
          url: url,
          data: {},
          dataType: "json",
          success: function (response) {
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              let data = response.data;
              self.user = new User(data);
            }
            self.loader = false;
            setTimeout(() => {
              M.updateTextFields();
            }, 1000);
          },
          error: function (error) {
            self.loader = false;
            M.toast({ html: "Ocurrió un error inesperado" });
            console.error(error);
          },
        });
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      self.debug ? console.log("mounted UserNewForm") : null;
      this.checkEditMode();
    });
  },
});
