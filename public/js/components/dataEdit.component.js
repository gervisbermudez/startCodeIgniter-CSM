var dataEdit = Vue.component("dataEdit", {
  template: "#dataEditComponent-template",
  props: {},
  data: function () {
    return {
      loader: true,
      data: {},
    };
  },
  mixins: [mixins],
  computed: {
    keys: function () {
      if (this.data.item) {
        return Object.keys(this.data.item);
      }
      return [];
    },
  },
  methods: {
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        this.loader = false;
      }, 3000);
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
      this.initPlugins();
      if (this.$route.params.data) {
        this.data = this.$route.params.data;
      }
    });
  },
});
