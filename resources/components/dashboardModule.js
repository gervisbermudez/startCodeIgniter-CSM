Vue.component('usersCollection', {
    template: `
        <ul class="collection">
				<li class="collection-header collection-item avatar">
					<h5>Usuarios</h5>
				</li>
				<li class="collection-item avatar" v-for="(user, index) in users" :key="index">
					<a :href="getUserLink(user)">
						<img :src="getUserAvatar(user)" alt="" class="circle">
						<span class="title">{{user.user_data.nombre + ' ' +user.user_data.apellido}}</span>
						<p>{{user.role}}</p>
					</a>
				</li>
		</ul>
    `,
    props: [],
    data: function () {
        return {
            debug: false,
            loader: false,
            users: [],
        }
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
                    setTimeout(() => {
                        M.AutoInit();
                    }, 2000);
                }
            });
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
            this.debug ? console.log('mounted: usersCollection') : null;
            this.getDataFromServe();
        });
    },
});

Vue.component('createContents', {
    template: `
        <div class="panel">
			<div class="title">
				<h5>Contenidos Creados</h5>
                <a data-position="left" data-delay="50" data-tooltip="Crear contenido" 
                    class='tooltipped dropdown-trigger btn right btn-floating halfway-fab waves-effect waves-light'
					href='#!' data-target='dropdown2'><i class="large material-icons">add</i></a>
				<ul id='dropdown2' class='dropdown-content'>
					<li v-for="(item, index) in forms_types" :key="index"><a
						:href="getFormsTypeUrl(item)">{{item.form_name}}</a></li>
				</ul>
			</div>
			<div class="content row">
				<div class="col s4" v-for="(item, index) in content" :key="index">
					<p>
						{{item.form_name}} <br>
						by {{item.username}}</p>
				</div>
			</div>
		</div>
    `,
    props: [],
    data: function () {
        return {
            debug: false,
            loader: false,
            forms_types: [],
            content: []
        }
    },
    methods: {
        getDataFromServe() {
            var self = this;
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
        },
        getFormsTypeUrl(formObject) {
            return BASEURL + 'admin/formularios/addData/' + formObject.id;
        }
    },
    mounted: function () {
        this.$nextTick(function () {
            this.debug ? console.log('mounted: createContents') : null;
            this.getDataFromServe();
        });
    },
});

var dashboardModule = new Vue({
    el: '#root',
    data: {
        loader: false,
    },
    computed: {
    },
    methods: {
    },
    mounted: function () {
        this.$nextTick(function () {
        });
    }
});