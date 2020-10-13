Vue.component("pageCard", {
  template: "#page-card-template",
  props: ["pages"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },

  methods: {
    contentText: function (page) {
      var span = document.createElement("span");
      span.innerHTML = page.content;
      let text = span.textContent || span.innerText;
      return text.substring(0, 220) + "...";
    },
    getPageFullPath: function (page) {
      if (page.status == 1) {
        return BASEURL + page.path;
      }
      return BASEURL + "admin/paginas/editar/" + page.page_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});
