var loginForm = new Vue({
    el: '#root',
    data: {
        username: '',
        password: '',
    },
    computed: {
        btnEnable: function () {
            return (!!this.username && !!this.password) ? true : false;
        }
    },
    methods: {
        login() {
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
                }
            });
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            console.log('mounted loginForm');
        });
    }
});