Vue.component("pageCard", {
  template: "#page-card-template",
  props: ["page"],
  data: function () {
    return {
      debug: false,
    };
  },
  computed: {
    contentText: function () {
      var span = document.createElement("span");
      span.innerHTML = this.page.content;
      let text = span.textContent || span.innerText;
      return text.substring(0, 220) + "...";
    },
  },
  methods: {
    getUserAvatar: function (user_id) {
      return "/public/img/user1.jpg";
    },
    getPageFullPath: function (path) {
      if (this.page.status == 1) {
        return BASEURL + path;
      }
      return BASEURL + "admin/paginas/editar/" + this.page.page_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});
