var EventNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    event_id: null,
    name: "",
    slug: "",
    slugManual: false,
    subtitle: "",
    address: "",
    online_url: "",
    location_type: "physical",
    all_day: false,
    date_start: "",
    date_end: "",
    date_start_date: "",
    date_start_time: "",
    date_end_date: "",
    date_end_time: "",
    content: "",
    status: true,
    visibility: true,
    date_publish: "",
    date_create: "",
    date_update: "",
    type: "event",
    categorie_id: "0",
    categories: [],
    user_id: null,
    user: null,
    mainImage: [],
    publishondate: true,
    datepublish: "",
    timepublish: "",
    formEndpoint: "api/v1/events",
    formIdField: "event_id",
  },
  mixins: [mixins, formMixin],
  computed: {
    btnEnable: function () {
      return !!this.name;
    },
    getDateTimePublish: function () {
      if (this.publishondate) {
        return null;
      }
      return this.datepublish && this.timepublish
        ? this.datepublish + " " + this.timepublish + ":00"
        : this.datepublish || null;
    },
    getMainImagenPath() {
      if (this.mainImage.length > 0) {
        return this.mainImage[0].file_id;
      }
      return null;
    },
    composedDateStart: function () {
      return this.composeDateTime(this.date_start_date, this.date_start_time, this.all_day, "00:00:00");
    },
    composedDateEnd: function () {
      if (!this.date_end_date) {
        return null;
      }
      return this.composeDateTime(this.date_end_date, this.date_end_time, this.all_day, "23:59:59");
    },
  },
  watch: {
    name: function (val) {
      if (!this.editMode && !this.slugManual) {
        this.slug = this.slugFromName(val);
      }
    },
  },
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  methods: {
    composeDateTime: function (datePart, timePart, allDay, allDayTime) {
      if (!datePart) {
        return null;
      }
      if (allDay) {
        return datePart + " " + allDayTime;
      }
      if (timePart) {
        var t = timePart.length === 5 ? timePart + ":00" : timePart;
        return datePart + " " + t;
      }
      return datePart + " 00:00:00";
    },
    slugFromName: function (name) {
      return String(name || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");
    },
    splitDateTime: function (value) {
      if (!value) {
        return { date: "", time: "" };
      }
      var parts = String(value).split(" ");
      return {
        date: parts[0] || "",
        time: parts[1] ? parts[1].substr(0, 5) : "",
      };
    },
    removeImage(index) {
      if (this.mainImage.length > 0) {
        this.mainImage.splice(index, 1);
      }
      if (this.mainImage.length == 0) {
        this.mainImage = [];
      }
    },
    getFileImagenPath(file) {
      return BASEURL + file.file_path.substr(2) + this.getFileImagenName(file);
    },
    getFileImagenName(file) {
      return file.file_name + "." + file.file_type;
    },
    copyCallcack(files) {
      let file = files[0];
      file = new ExplorerFile(file);
      this.mainImage.push(file);
      let instance = M.Modal.getInstance($("#fileUploader"));
      instance.close();
    },
    setTinyContent: function (html) {
      if (typeof tinymce === "undefined" || !tinymce.editors["id_cazary"]) {
        return;
      }
      tinymce.editors["id_cazary"].setContent(html || "");
    },
    validateForm() {
      if (!this.name) {
        return false;
      }
      if (this.location_type === "online" || this.location_type === "hybrid") {
        if (!String(this.online_url || "").trim()) {
          return false;
        }
      }
      if (this.status) {
        if (!this.composedDateStart) {
          return false;
        }
        var text = String(this.content || "").replace(/<[^>]+>/g, "").replace(/&nbsp;/g, " ").trim();
        if (!text) {
          return false;
        }
      }
      return true;
    },
    save() {
      var self = this;
      if (typeof tinymce !== "undefined" && tinymce.editors["id_cazary"]) {
        this.content = tinymce.editors["id_cazary"].getContent();
      }
      if (self.validateForm()) {
        this.loader = true;
        this.runSaveData(function () {
          self.toast("toast_saved");
        });
      } else {
        this.toast("toast_form_invalid");
      }
    },
    getData: function () {
      return {
        event_id: this.event_id || "",
        name: this.name || "",
        slug: this.slug || "",
        subtitle: this.subtitle || "",
        content: this.content || "",
        address: this.address || "",
        online_url: this.online_url || "",
        location_type: this.location_type || "physical",
        all_day: this.all_day ? 1 : 0,
        date_start: this.composedDateStart || "",
        date_end: this.composedDateEnd || "",
        mainImage: this.getMainImagenPath,
        categorie_id: this.categorie_id || 0,
        status: this.status ? 1 : 2,
        visibility: this.visibility ? 1 : 0,
        date_publish: this.getDateTimePublish || "",
      };
    },
    afterSave: function (response) {
      this.editMode = true;
      this.slugManual = true;
      if (this.formIdField && response && response.data) {
        this[this.formIdField] = response.data[this.formIdField];
        if (response.data.slug) {
          this.slug = response.data.slug;
        }
        if (response.data.date_create) {
          this.date_create = response.data.date_create;
        }
        if (response.data.date_publish) {
          this.date_publish = response.data.date_publish;
        }
        if (response.data.date_update) {
          this.date_update = response.data.date_update;
        }
      }
    },
    getCategories() {
      this.debug ? console.log(`${getFuncName()} fired`) : null;
      var self = this;
      var url = BASEURL + "api/v1/categories/type/" + self.type;
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          self.loader = false;
          if (response.code == 200) {
            self.categories = response.data;
            this.initSelects();
          }
        })
        .catch((err) => {
          self.debug ? console.log(err) : null;
          self.loader = false;
        });
    },
    checkEditMode() {
      var self = this;
      if (event_id && editMode == "edit") {
        self.editMode = true;
        self.slugManual = true;
        var url = BASEURL + "api/v1/events/" + event_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            if (response.code == 200) {
              var d = response.data;
              self.event_id = d.event_id;
              self.date_create = d.date_create;
              self.date_publish = d.date_publish;
              self.date_update = d.date_update;
              self.name = d.name;
              self.slug = d.slug || "";
              self.subtitle = d.subtitle;
              self.categorie_id = d.categorie_id;
              self.status = d.status == 1 || d.status == "1";
              self.visibility = d.visibility == 1 || d.visibility == "1" || d.visibility === true;
              self.address = d.address;
              self.online_url = d.online_url || "";
              self.location_type = d.location_type || "physical";
              self.all_day = d.all_day == 1 || d.all_day == "1" || d.all_day === true;
              self.date_start = d.date_start;
              self.date_end = d.date_end;
              var startParts = self.splitDateTime(d.date_start);
              self.date_start_date = startParts.date;
              self.date_start_time = startParts.time;
              var endParts = self.splitDateTime(d.date_end);
              self.date_end_date = endParts.date;
              self.date_end_time = endParts.time;
              self.user_id = d.user_id;
              self.user = new User(d.user);
              self.content = d.content;
              if (d.date_publish) {
                var pub = self.splitDateTime(d.date_publish);
                self.datepublish = pub.date;
                self.timepublish = pub.time;
              }
              if (d.mainImage) {
                self.mainImage.push(new ExplorerFile(d.mainImage));
              }
              self.setTinyContent(d.content);
              self.$nextTick(function () {
                self.initSelects();
              });
            }
          })
          .catch((response) => {
            M.toast({ html: response.error_message });
            self.loader = false;
          });
      } else {
        self.loader = false;
      }
    },
    initSelects() {
      this.$nextTick(function () {
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
      });
    },
    initDateTimePickers: function () {
      var self = this;
      var bindDate = function (id, field) {
        var el = document.getElementById(id);
        if (!el) {
          return;
        }
        M.Datepicker.init(el, {
          format: "yyyy-mm-dd",
          onClose: function () {
            self[field] = el.value;
          },
        });
      };
      var bindTime = function (id, field) {
        var el = document.getElementById(id);
        if (!el) {
          return;
        }
        M.Timepicker.init(el, {
          twelveHour: false,
          defaultTime: "now",
          onCloseEnd: function () {
            self[field] = el.value;
          },
        });
      };
      bindDate("event_date_start", "date_start_date");
      bindDate("event_date_end", "date_end_date");
      bindDate("datepublish", "datepublish");
      bindTime("event_time_start", "date_start_time");
      bindTime("event_time_end", "date_end_time");
      bindTime("timepublish", "timepublish");
    },
    initPlugins() {
      this.initDateTimePickers();
      var self = this;
      tinymce.init({
        base_url: BASEURL + "/public/vendors/tinymce/js/tinymce",
        selector: "#id_cazary",
        plugins: ["link table code"],
        init_instance_callback: function (editor) {
          if (self.content) {
            editor.setContent(self.content);
          }
        },
        setup: (editor) => {
          editor.on("Change", (e) => {
            this.content = editor.getContent();
          });
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getCategories();
      this.initPlugins();
      this.checkEditMode();
    });
  },
});
