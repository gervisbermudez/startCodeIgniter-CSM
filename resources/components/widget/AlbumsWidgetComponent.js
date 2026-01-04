Vue.component("albumesWidget", {
  template: "#albumes-widget-template",
  props: ["albumes"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {
    getPageImagePath(album, index) {
      let item = album.items[index] ? album.items[index] : { file: {} };
      if (item.file.file_front_path) {
        return BASEURL + item.file.file_front_path;
      }
      return BASEURL + "/public/img/default.jpg";
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});
