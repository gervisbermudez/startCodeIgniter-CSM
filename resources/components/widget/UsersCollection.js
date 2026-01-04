Vue.component("usersCollection", {
  template: "#user-collection-template",
  props: ["users"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  methods: {},
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: usersCollection") : null;
    });
  },
});
