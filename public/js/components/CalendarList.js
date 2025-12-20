var calendar; 

var CalendarList = new Vue({
  el: "#root",
  data: {
    loader: true,
    searchResults: false,
    events: [],
    allEvents: [],
    filters: {
      pages: true,
      albums: true,
      users: true,
      files: false,
      categories: true,
      menus: true,
      siteforms: true,
      siteform_submits: false,
      form_customs: true,
      form_contents: false
    }
  },
  mixins: [mixins],
  computed: {},
  methods: {
    parseDateTime(strDateTime) {
      if (!strDateTime) return null;
      
      try {
        var dateString = strDateTime;
        var reggie = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/;
        var dateArray = reggie.exec(dateString);
        
        if (!dateArray) {
          console.warn('Invalid date format:', strDateTime);
          return null;
        }
        
        var dateObject = new Date(
          +dateArray[1],
          +dateArray[2] - 1, // Careful, month starts at 0!
          +dateArray[3],
          +dateArray[4],
          +dateArray[5],
          +dateArray[6]
        );
        
        // Validate the date
        if (isNaN(dateObject.getTime())) {
          console.warn('Invalid date:', strDateTime);
          return null;
        }
        
        return dateObject;
      } catch (error) {
        console.error('Error parsing date:', strDateTime, error);
        return null;
      }
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

            // Pages
            if (response.data.pages && Array.isArray(response.data.pages)) {
              response.data.pages.forEach((element) => {
                const startDate = self.parseDateTime(element.date_publish);
                if (startDate) {
                  self.allEvents.push({
                    id: "page_" + element.page_id,
                    title: "📄 " + element.title,
                    url: self.base_url("admin/pages/view/" + element.page_id),
                    start: startDate,
                    end: startDate,
                    color: '#1976d2',
                    extendedProps: {
                      type: 'pages'
                    }
                  });
                }
              });
            }

            // Albums
            if (response.data.albumes && Array.isArray(response.data.albumes)) {
              response.data.albumes.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "album_" + element.album_id,
                    title: "🖼️ " + element.name,
                    url: self.base_url("admin/gallery/items/" + element.album_id),
                    start: startDate,
                    end: startDate,
                    color: '#388e3c',
                    extendedProps: {
                      type: 'albums'
                    }
                  });
                }
              });
            }

            // Users
            if (response.data.users && Array.isArray(response.data.users)) {
              response.data.users.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "user_" + element.user_id,
                    title: "👤 " + element.get_fullname(),
                    url: element.get_profileurl(),
                    start: startDate,
                    end: startDate,
                    color: '#7b1fa2',
                    extendedProps: {
                      type: 'users'
                    }
                  });
                }
              });
            }

            // Files
            if (response.data.files && Array.isArray(response.data.files)) {
              response.data.files.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "file_" + element.file_id,
                    title: "📁 " + element.name,
                    url: self.base_url("admin/files"),
                    start: startDate,
                    end: startDate,
                    color: '#f57c00',
                    extendedProps: {
                      type: 'files'
                    }
                  });
                }
              });
            }

            // Categories
            if (response.data.categories && Array.isArray(response.data.categories)) {
              response.data.categories.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "category_" + element.categorie_id,
                    title: "🏷️ " + element.name,
                    url: self.base_url("admin/categories/editar/" + element.categorie_id),
                    start: startDate,
                    end: startDate,
                    color: '#c2185b',
                    extendedProps: {
                      type: 'categories'
                    }
                  });
                }
              });
            }

            // Menus
            if (response.data.menus && Array.isArray(response.data.menus)) {
              response.data.menus.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "menu_" + element.menu_id,
                    title: "🧭 " + element.name,
                    url: self.base_url("admin/menus/editar/" + element.menu_id),
                    start: startDate,
                    end: startDate,
                    color: '#0097a7',
                    extendedProps: {
                      type: 'menus'
                    }
                  });
                }
              });
            }

            // Site Forms
            if (response.data.siteforms && Array.isArray(response.data.siteforms)) {
              response.data.siteforms.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "siteform_" + element.siteform_id,
                    title: "📋 " + element.name,
                    url: self.base_url("admin/siteforms/editar/" + element.siteform_id),
                    start: startDate,
                    end: startDate,
                    color: '#5d4037',
                    extendedProps: {
                      type: 'siteforms'
                    }
                  });
                }
              });
            }

            // Form Submissions
            if (response.data.siteform_submits && Array.isArray(response.data.siteform_submits)) {
              response.data.siteform_submits.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "siteform_submit_" + element.siteform_submit_id,
                    title: "✉️ Form Submit #" + element.siteform_submit_id,
                    url: self.base_url("admin/siteforms/editar/" + element.siteform_id),
                    start: startDate,
                    end: startDate,
                    color: '#455a64',
                    extendedProps: {
                      type: 'siteform_submits'
                    }
                  });
                }
              });
            }

            // Custom Models
            if (response.data.form_customs && Array.isArray(response.data.form_customs)) {
              response.data.form_customs.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "custom_model_" + element.custom_model_id,
                    title: "⚙️ " + element.form_name,
                    url: self.base_url("admin/custommodels/editar/" + element.custom_model_id),
                    start: startDate,
                    end: startDate,
                    color: '#d32f2f',
                    extendedProps: {
                      type: 'form_customs'
                    }
                  });
                }
              });
            }

            // Custom Model Contents
            if (response.data.form_contents && Array.isArray(response.data.form_contents)) {
              response.data.form_contents.forEach((element) => {
                const startDate = self.parseDateTime(element.date_create);
                if (startDate) {
                  self.allEvents.push({
                    id: "form_content_" + element.custom_model_content_id,
                    title: "📝 Content #" + element.custom_model_content_id,
                    url: self.base_url("admin/custommodels/items/" + element.custom_model_id),
                    start: startDate,
                    end: startDate,
                    color: '#e64a19',
                    extendedProps: {
                      type: 'form_contents'
                    }
                  });
                }
              });
            }
            
          }

          self.loader = false;
          self.applyFilters();
          self.init();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    applyFilters() {
      this.events = this.allEvents.filter(event => {
        return this.filters[event.extendedProps.type];
      });
      if (calendar) {
        calendar.removeAllEvents();
        calendar.addEventSource(this.events);
      }
    },
    toggleFilter(type) {
      this.filters[type] = !this.filters[type];
      this.applyFilters();
    },
    init() {
      var calendarEl = document.getElementById("calendar");
      calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        schedulerLicenseKey: "GPL-My-Project-Is-Open-Source",
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
        },
        views: {
          listMonth: {
            buttonText: 'Lista'
          }
        },
        eventClick: function(info) {
          info.jsEvent.preventDefault(); // don't let the browser navigate
          if (info.event.url) {
            window.location.href = info.event.url;
          }
        },
        events: this.events,
        eventTimeFormat: { // like '14:30'
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        },
        locale: 'es'
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
