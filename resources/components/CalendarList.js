var calendar; 

var CalendarList = new Vue({
  el: "#root",
  data: {
    loader: true,
    searchResults: false,
    events: [],
  },
  mixins: [mixins],
  computed: {},
  methods: {
    parseDateTime(strDateTime) {
      var dateString = strDateTime;
      var reggie = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/;
      var dateArray = reggie.exec(dateString);
      var dateObject = new Date(
        +dateArray[1],
        +dateArray[2] - 1, // Careful, month starts at 0!
        +dateArray[3],
        +dateArray[4],
        +dateArray[5],
        +dateArray[6]
      );
      return dateObject;
    },
    performSearch: function () {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/search/?q=1",
        data: {},
        dataType: "json",
        success: function (response) {
          self.searchResults = response.data;
          self.searchResults.users = response.data.users.map((element) => {
            return new User(element);
          });

          if (response.data) {

            response.data.pages.forEach((element) => {
              self.events.push({
                id: "page_" + element.page_id,
                title: element.title,
                url: self.base_url("admin/paginas/view/" + element.page_id),
                start: self.parseDateTime(element.date_publish),
                end: self.parseDateTime(element.date_publish),
              });
            });

            response.data.albumes.forEach((element) => {
              self.events.push({
                id: "album_" + element.album_id,
                title: element.name,
                url: self.base_url("admin/galeria/items/" + element.album_id),
                start: self.parseDateTime(element.date_create),
                end: self.parseDateTime(element.date_create),
              });
            });

            response.data.users.forEach((element) => {
              self.events.push({
                id: "user_" + element.user_id,
                title: element.get_fullname(),
                url: element.get_profileurl(),
                start: self.parseDateTime(element.date_create),
                end: self.parseDateTime(element.date_create),
              });
            });
            
          }

          self.loader = false;
          self.init();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    init() {
      var calendarEl = document.getElementById("calendar");
      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        schedulerLicenseKey: "GPL-My-Project-Is-Open-Source",
        initialDate: "2020-12-07",
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
        },
        events: this.events,
      });

      calendar.render();
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.performSearch();
    });
  },
});
