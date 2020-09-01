Vue.component("pageCard", {
  template: "#page-card-template",
  data: function () {
    return {
      debug: DEBUGMODE,
      pages: [],
    };
  },
  computed: {
    /*filterPages: function () {
       if (this.pages.length > 3) {
        return this.pages.slice(0, 3);
      } 
      return this.pages;
    },*/
  },
  methods: {
    contentText: function (page) {
      var span = document.createElement("span");
      span.innerHTML = page.content;
      let text = span.textContent || span.innerText;
      return text.substring(0, 220) + "...";
    },
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/",
        data: {},
        dataType: "json",
        success: function (response) {
          let pages = response.data;
          self.pages = pages.map((page) => {
            page.user = new User(page.user);
            return page;
          });
        },
        error: function (error) {
          console.error(error);
        },
      });
    },
    getPageFullPath: function (page) {
      if (page.status == 1) {
        return BASEURL + page.path;
      }
      return BASEURL + "admin/paginas/editar/" + page.page_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getPages();
    });
  },
});
