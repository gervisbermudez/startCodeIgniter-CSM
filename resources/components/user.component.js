var usersModule = new Vue({
    el: '#root',
    data: {
        users: [],
        loader: true,
        BASEURL: ''
    },
    mounted: function () {
        this.$nextTick(function () {
            this.BASEURL = BASEURL;
            axios.get(BASEURL + 'api/v1/user')
            .then(function (response) {
                // handle success
                usersModule.users = response.data;
                usersModule.loader = false;
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
            .finally(function () {
                // always executed
            });
        });
    }
});