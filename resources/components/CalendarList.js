var CalendarList = new Vue({
  el: "#root",
  data: {
    loader: true,
    booted: false,
    types: {
      events: true,
    },
    items: [],
    rangeFrom: null,
    rangeTo: null,
    selected: null,
    fc: null,
    canCreate: !!(window.CALENDAR_PERMS && window.CALENDAR_PERMS.create),
    canUpdate: !!(window.CALENDAR_PERMS && window.CALENDAR_PERMS.update),
    selectEvents: !!(window.CALENDAR_PERMS && window.CALENDAR_PERMS.selectEvents),
  },
  mixins: [mixins],
  computed: {
    isEmpty: function () {
      return !this.loader && this.items.length === 0;
    },
  },
  created: function () {
    if (!this.selectEvents) {
      this.types.events = false;
    }
  },
  methods: {
    pad2: function (n) {
      return n < 10 ? "0" + n : String(n);
    },
    formatDateTime: function (d) {
      return (
        d.getFullYear() +
        "-" +
        this.pad2(d.getMonth() + 1) +
        "-" +
        this.pad2(d.getDate()) +
        " " +
        this.pad2(d.getHours()) +
        ":" +
        this.pad2(d.getMinutes()) +
        ":" +
        this.pad2(d.getSeconds())
      );
    },
    ymd: function (d) {
      return d.getFullYear() + "-" + this.pad2(d.getMonth() + 1) + "-" + this.pad2(d.getDate());
    },
    addDaysYmd: function (ymdStr, days) {
      var parts = String(ymdStr).split("-");
      var d = new Date(+parts[0], +parts[1] - 1, +parts[2]);
      d.setDate(d.getDate() + days);
      return this.ymd(d);
    },
    parseDateTime: function (strDateTime) {
      if (!strDateTime) {
        return null;
      }
      var raw = String(strDateTime).trim();
      var m = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?/.exec(raw);
      if (m) {
        var withTime = new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5], m[6] ? +m[6] : 0);
        if (!isNaN(withTime.getTime())) {
          return withTime;
        }
      }
      var dOnly = /^(\d{4})-(\d{2})-(\d{2})$/.exec(raw);
      if (dOnly) {
        var midnight = new Date(+dOnly[1], +dOnly[2] - 1, +dOnly[3], 0, 0, 0);
        if (!isNaN(midnight.getTime())) {
          return midnight;
        }
      }
      var nativeDate = new Date(raw);
      if (!isNaN(nativeDate.getTime())) {
        return nativeDate;
      }
      return null;
    },
    isAllDay: function (item) {
      return item && (item.allDay === true || item.allDay === 1 || item.allDay === "1");
    },
    eventColor: function (item) {
      var status = parseInt(item.status, 10);
      if (status === 2) {
        return "#757575";
      }
      if (status === 3) {
        return "#ff9800";
      }
      return "#26A69A";
    },
    toFcEvents: function (items) {
      var self = this;
      var out = [];
      (items || []).forEach(function (item) {
        var startParsed = self.parseDateTime(item.start);
        if (!startParsed) {
          return;
        }
        var endParsed = self.parseDateTime(item.end) || startParsed;
        var allDay = self.isAllDay(item);
        var fcEvent = {
          id: item.id,
          title: item.title,
          color: self.eventColor(item),
          classNames: ["calendar-fc-event"],
          extendedProps: { payload: item, type: item.type },
        };
        if (allDay) {
          fcEvent.allDay = true;
          fcEvent.start = self.ymd(startParsed);
          fcEvent.end = self.addDaysYmd(self.ymd(endParsed), 1);
        } else {
          fcEvent.allDay = false;
          fcEvent.start = startParsed;
          if (item.end) {
            fcEvent.end = endParsed;
          }
        }
        if (parseInt(item.status, 10) === 2) {
          fcEvent.classNames.push("calendar-fc-event--draft");
        }
        out.push(fcEvent);
      });
      return out;
    },
    typesCsv: function () {
      return this.types.events ? "events" : "none";
    },
    loadFeed: function () {
      var self = this;
      if (!this.rangeFrom || !this.rangeTo) {
        return;
      }
      if (!this.booted) {
        this.loader = true;
      }
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/calendar",
        data: {
          from: this.rangeFrom,
          to: this.rangeTo,
          types: this.typesCsv(),
        },
        dataType: "json",
        success: function (response) {
          self.booted = true;
          self.loader = false;
          if (response && response.code == 200 && response.data) {
            self.items = response.data.items || [];
          } else {
            self.items = [];
          }
          self.syncCalendar();
          self.$nextTick(function () {
            if (self.fc && typeof self.fc.updateSize === "function") {
              self.fc.updateSize();
            }
          });
        },
        error: function (xhr) {
          self.booted = true;
          self.loader = false;
          self.items = [];
          self.toastError(xhr);
        },
      });
    },
    syncCalendar: function () {
      if (!this.fc) {
        return;
      }
      this.fc.removeAllEvents();
      this.fc.addEventSource(this.toFcEvents(this.items));
    },
    statusLabel: function (status) {
      var n = parseInt(status, 10);
      if (n === 1) {
        return this.lang("published");
      }
      if (n === 2) {
        return this.lang("draft");
      }
      if (n === 3) {
        return this.lang("archived");
      }
      return String(status || "");
    },
    statusClass: function (status) {
      var n = parseInt(status, 10);
      if (n === 1) {
        return "status-published";
      }
      if (n === 2) {
        return "status-draft";
      }
      if (n === 3) {
        return "status-archived";
      }
      return "status-draft";
    },
    canEditSelected: function (item) {
      return !!(item && item.type === "events" && this.canUpdate);
    },
    formatSelectedWhen: function (item) {
      if (!item) {
        return "";
      }
      var start = this.parseDateTime(item.start);
      var end = this.parseDateTime(item.end) || start;
      if (!start) {
        return "";
      }
      if (this.isAllDay(item)) {
        var startDay = this.ymd(start);
        var endDay = this.ymd(end);
        var label = startDay === endDay ? startDay : startDay + " – " + endDay;
        return label + " · " + this.lang("events_all_day");
      }
      var sameDay = this.ymd(start) === this.ymd(end);
      var startHm = this.pad2(start.getHours()) + ":" + this.pad2(start.getMinutes());
      var endHm = this.pad2(end.getHours()) + ":" + this.pad2(end.getMinutes());
      if (sameDay) {
        return this.ymd(start) + " " + startHm + " – " + endHm;
      }
      return this.ymd(start) + " " + startHm + " – " + this.ymd(end) + " " + endHm;
    },
    onEsc: function (ev) {
      if (ev.key === "Escape" || ev.keyCode === 27) {
        this.selected = null;
      }
    },
    init: function () {
      var self = this;
      var calendarEl = document.getElementById("calendar");
      if (!calendarEl || typeof FullCalendar === "undefined") {
        this.loader = false;
        return;
      }
      this.fc = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        schedulerLicenseKey: "GPL-My-Project-Is-Open-Source",
        locale: window.CALENDAR_LOCALE || "en",
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
        },
        views: {
          listMonth: {
            buttonText: self.lang("calendar_list"),
          },
        },
        eventTimeFormat: {
          hour: "2-digit",
          minute: "2-digit",
          hour12: false,
        },
        datesSet: function (info) {
          self.rangeFrom = self.formatDateTime(info.start);
          self.rangeTo = self.formatDateTime(info.end);
          self.loadFeed();
        },
        eventClick: function (info) {
          info.jsEvent.preventDefault();
          var payload = info.event.extendedProps && info.event.extendedProps.payload;
          if (payload) {
            self.selected = payload;
          }
        },
        dateClick: function (info) {
          if (!self.canCreate) {
            return;
          }
          var dateStr = info.dateStr ? String(info.dateStr).substr(0, 10) : self.ymd(info.date);
          window.location.href = self.base_url("admin/events/add?date=" + dateStr);
        },
        events: [],
      });
      this.fc.render();
    },
  },
  mounted: function () {
    var self = this;
    document.addEventListener("keydown", this.onEsc);
    this.$nextTick(function () {
      self.init();
      self.initPlugins();
    });
  },
  beforeDestroy: function () {
    document.removeEventListener("keydown", this.onEsc);
    if (this.fc) {
      this.fc.destroy();
    }
  },
});
