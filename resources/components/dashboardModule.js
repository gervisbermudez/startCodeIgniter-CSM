var dashboardModule = new Vue({
    el: '#root',
    data: {
        users: [],
        loader: true,
        forms_types: [],
        content: []
    },
    computed: {
    },
    methods: {
        getDataFromServe() {
            var self = this;

            $.ajax({
                type: "GET",
                url: BASEURL + 'api/v1/user',
                data: {},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    self.users = response;
                    self.loader = false;
                }
            });

            $.ajax({
                type: "GET",
                url: BASEURL + "admin/formularios/ajax_get_forms_data",
                data: {},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    self.content = response.data;
                }
            });

            $.ajax({
                type: "GET",
                url: BASEURL + "admin/formularios/ajax_get_forms_types",
                data: {},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    self.forms_types = response.data;
                }
            });

            setTimeout(() => {
                M.AutoInit();
            }, 2000);
        },
        getFormsTypeUrl(formObject) {
            return BASEURL + 'admin/formularios/addData/' + formObject.id;
        },
        getUserAvatar(user) {
            if (user.user_data.avatar) {
                return BASEURL + 'public/img/profile/' + user.username + '/' + user.user_data.avatar;
            } else {
                return "https://materializecss.com/images/sample-1.jpg";
            }
        },
        getUserLink(user) {
            return BASEURL + '/admin/user/ver/' + user.id;
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            this.getDataFromServe();
        });
    }
});