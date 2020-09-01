var dashboardModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
  },
  mounted: function () {
    this.$nextTick(function () {
      setTimeout(() => {
        M.AutoInit();
        this.loader = false;
      }, 3000);
    });
  },
});
