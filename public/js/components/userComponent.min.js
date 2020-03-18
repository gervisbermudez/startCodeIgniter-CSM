Vue.component('userCard', {
    template: '#user-card-template',
    props: ['user'],
    data: function () {
        return {

        }
    },
    methods: {
        getAvatarUrl(){
            return BASEURL + 'public/img/profile/' + this.user.username + '/' + this.user.user_data.avatar;
        },
        getUserUrl(){
            return BASEURL + 'admin/usuarios/ver/' + this.user.user_id;
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            
        });
    },
});

var usersModule = new Vue({
    el: '#root',
    data: {
        users: [],
        loader: true,
    },
    mounted: function () {
        this.$nextTick(function () {
            var self = this;
            $.ajax({
                type: "GET",
                url: BASEURL + "admin/usuarios/ajax_get_users",
                data: {},
                dataType: "json",
                success: function (response) {
                    self.users = response.data;
                    self.loader = false;
                },
                error: function (error) {
                    M.toast({ html: response.responseJSON.error_message });
                    self.loader = false;
                }
            });
        });
    }
});