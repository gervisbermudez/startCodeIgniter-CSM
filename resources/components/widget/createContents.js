Vue.component("createContents", {
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
      content: [],
    };
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
        },
      });

      $.ajax({
        type: "GET",
        url: BASEURL + "admin/formularios/ajax_get_forms_data",
        data: {},
        dataType: "json",
        success: function (response) {
          console.log(response);
          self.content = response.data;
        },
      });
    },
    getFormsTypeUrl(formObject) {
      return BASEURL + "admin/formularios/addData/" + formObject.id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: createContents") : null;
      this.getDataFromServe();
    });
  },
});
