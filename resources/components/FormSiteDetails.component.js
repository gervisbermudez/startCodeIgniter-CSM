var dataDeatils = Vue.component("FormSiteDetails", {
  template: "#FormSiteDetails-template",
  props: {},
  data: function () {
    return {
      loader: true,
      endpoint: "api/v1/siteforms/submit/",
      data: {
        siteform_submit_data: {},
      },
      user: {},
    };
  },
  mixins: [mixins],
  computed: {
    keys: function () {
      if (this.data.siteform_submit_data) {
        return Object.keys(this.data.siteform_submit_data);
      }
      return [];
    },
  },
  methods: {
    back: function () {
      this.$router.push({
        name: "table",
      });
    },
    initPlugins: function () {
      setTimeout(() => {
        M.Tabs.init(document.getElementById("formTabs"), {});
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        this.loader = false;
      }, 100);
    },
    getData: function () {
      this.loader = true;
      var url = BASEURL + this.endpoint + this.$route.params.siteform_submit_id;
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          let data = response.data;
          this.data = data;
          this.user = new User(data.siteform.user);
          this.loader = false;
          this.initPlugins();
        })
        .catch((response) => {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
        });
    },
    setArchive: function () {
      var url =
        BASEURL +
        "api/v1/siteforms/submit_archive/" +
        this.$route.params.siteform_submit_id;
      fetch(url, {
        method: "POST",
      })
        .then((response) => response.json())
        .then((response) => {
          console.log(response);
          if (response.data) {
            this.data.status = 2;
            M.toast({ html: "Se marcó como leido" });
          }
        })
        .catch((error) => {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        });
    },
    save: function () {
      this.$router.push({
        name: "table",
        params: {
          data: this.data,
        },
      });
      this.$root.$emit("eventing", this.data);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      if (this.$route.params.siteform_submit_id) {
        this.getData();
      } else {
        this.$router.push({
          name: "table",
        });
      }
    });
  },
});
