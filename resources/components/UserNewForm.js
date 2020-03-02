var loginForm = new Vue({
    el: '#root',
    data: {
        loader: true,
        editMode: false,
        user_id: '',
        username: '',
        password: '',
        email: '',
        usergroup_id: '',
        user_data: {
            nombre: '',
            apellido: '',
            direccion: '',
            telefono: ''
        },
        status: true,
    },
    computed: {
        btnEnable: function () {
            return (!!this.username && !!this.password) ? true : false;
        }
    },
    methods: {
        save() {
            var self = this;
            this.loader = true;
            $.ajax({
                type: "POST",
                url: BASEURL + "admin/login/ajax_verify_auth",
                data: {
                    username: this.username,
                    password: this.password
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    window.location = BASEURL + response.redirect;
                },
                error: function (response) {
                    M.toast({ html: response.responseJSON.error_message });
                    self.loader = false;
                }
            });
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            console.log('mounted UserNewForm');
            this.loader = false;
        });
    }
});