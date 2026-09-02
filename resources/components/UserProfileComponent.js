var userProfile = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    user: new User(),
    profile: window.USER_PROFILE || {},
    summary: {
      counts: {},
      drafts: [],
      recent_files: [],
      permissions: [],
      last_login: null,
    },
    timelineGroups: {},
    timelinePage: 0,
    timelineHasMore: true,
    timelineLoading: false,
    logs: [],
    logsPage: 0,
    logsHasMore: true,
    logsLoading: false,
    logsLoaded: false,
    accountLoaded: false,
    activeTab: "activity",
    toDeleteItem: {},
    _timelineObserver: null,
    _logsObserver: null,
  },
  mixins: [mixins],
  computed: {
    isActive: function () {
      return parseInt(this.user.status, 10) === 1;
    },
    contactEmail: function () {
      return this.hasText(this.user.email);
    },
    contactPhone: function () {
      var data = this.user.user_data || {};
      return this.hasText(data.telefono);
    },
    contactAddress: function () {
      var data = this.user.user_data || {};
      return this.hasText(data.direccion);
    },
    profileCargo: function () {
      var data = this.user.user_data || {};
      return this.hasText(data.cargo);
    },
    profileBio: function () {
      var data = this.user.user_data || {};
      return this.hasText(data.bio);
    },
    timelineDateKeys: function () {
      return Object.keys(this.timelineGroups);
    },
    kpiCards: function () {
      var can = (this.profile && this.profile.can) || {};
      return [
        { key: "pages", icon: "web", href: "admin/pages", can: !!can.pages, labelKey: "user_profile_kpi_pages" },
        { key: "collections", icon: "view_module", href: "admin/custommodels", can: !!can.collections, labelKey: "user_profile_kpi_collections" },
        { key: "items", icon: "view_list", href: "admin/custommodels/content", can: !!can.items, labelKey: "user_profile_kpi_items" },
        { key: "files", icon: "folder", href: "admin/files", can: !!can.files, labelKey: "user_profile_kpi_files" },
      ];
    },
    breakdownRows: function () {
      var counts = this.summary.counts || {};
      var can = (this.profile && this.profile.can) || {};
      var rows = [
        { key: "fragments", count: counts.fragments, href: "admin/fragments", can: !!can.fragments, labelKey: "menu_fragments", icon: "bookmark_border", tone: "accent" },
        { key: "albums", count: counts.albums, href: "admin/gallery", can: !!can.albums, labelKey: "menu_albums", icon: "photo", tone: "warning" },
        { key: "events", count: counts.events, href: "admin/events", can: !!can.events, labelKey: "menu_events", icon: "event", tone: "interactive" },
        { key: "menus", count: counts.menus, href: "admin/menus", can: !!can.menus, labelKey: "menu_menus", icon: "view_list", tone: "chrome" },
        { key: "siteforms", count: counts.siteforms, href: "admin/siteforms", can: !!can.siteforms, labelKey: "menu_siteforms", icon: "inbox", tone: "success" },
      ];
      return rows.filter(function (row) {
        return parseInt(row.count, 10) > 0;
      });
    },
    showRecentFiles: function () {
      return !!(
        this.profile &&
        this.profile.canSelectFiles &&
        this.summary.recent_files &&
        this.summary.recent_files.length
      );
    },
  },
  methods: {
    profileUserId: function () {
      var id = this.profile && this.profile.userId;
      return id ? String(id) : "";
    },
    hasText: function (value) {
      if (value === null || value === undefined) {
        return "";
      }
      var text = String(value).trim();
      if (text === "" || text.toLowerCase() === "undefined" || text.toLowerCase() === "null") {
        return "";
      }
      return text;
    },
    countOf: function (key) {
      var counts = this.summary.counts || {};
      var n = parseInt(counts[key], 10);
      return isNaN(n) ? 0 : n;
    },
    canGo: function (key) {
      var can = (this.profile && this.profile.can) || {};
      return !!can[key];
    },
    truncateText: function (value, max) {
      var text = value ? String(value) : "";
      if (text.length <= max) {
        return text;
      }
      return text.substring(0, max) + "...";
    },
    fetchJson: function (url) {
      return fetch(url, { credentials: "same-origin" }).then(function (res) {
        return res.json();
      });
    },
    getUser: function () {
      var self = this;
      var user_id = this.profileUserId();
      if (!user_id) {
        self.loader = false;
        self.toast("toast_error");
        return;
      }
      self.loader = true;
      var url = BASEURL + "api/v1/users/" + user_id;
      this.fetchJson(url)
        .then(function (response) {
          self.debug ? console.log(url, response) : null;
          self.loader = false;
          if (response && response.code == 200) {
            self.user = new User(response.data);
            self.$nextTick(function () {
              self.initTabs();
              self.initPlugins();
              self.observeTimelineSentinel();
            });
            return;
          }
          self.toast("toast_error");
        })
        .catch(function (err) {
          self.loader = false;
          self.toast("toast_error");
          console.error(err);
        });
    },
    getSummary: function () {
      var self = this;
      var user_id = this.profileUserId();
      if (!user_id) {
        return;
      }
      var url = BASEURL + "api/v1/users/summary/" + user_id;
      this.fetchJson(url)
        .then(function (response) {
          self.debug ? console.log(url, response) : null;
          if (response && response.code == 200 && response.data) {
            self.summary = Object.assign(
              { counts: {}, drafts: [], recent_files: [], permissions: [], last_login: null },
              response.data
            );
            if (!self.summary.counts) {
              self.summary.counts = {};
            }
            if (!self.summary.drafts) {
              self.summary.drafts = [];
            }
            if (!self.summary.recent_files) {
              self.summary.recent_files = [];
            }
            if (!self.summary.permissions) {
              self.summary.permissions = [];
            }
            return;
          }
          self.toast("toast_error");
        })
        .catch(function (err) {
          self.toast("toast_error");
          console.error(err);
        });
    },
    loadTimelinePage: function () {
      var self = this;
      if (self.timelineLoading || !self.timelineHasMore) {
        return;
      }
      var user_id = this.profileUserId();
      if (!user_id) {
        return;
      }
      self.timelineLoading = true;
      var next = self.timelinePage + 1;
      var url = BASEURL + "api/v1/users/timeline/" + user_id + "?page=" + next + "&per_page=20";
      this.fetchJson(url)
        .then(function (response) {
          self.debug ? console.log(url, response) : null;
          self.timelineLoading = false;
          if (!(response && response.code == 200)) {
            self.toast("toast_error");
            return;
          }
          self.timelinePage = next;
          var items = Array.isArray(response.data) ? response.data : [];
          self.appendTimelineItems(items);
          var totalPages = parseInt(response.total_pages, 10) || 0;
          if (items.length === 0 || (totalPages > 0 && next >= totalPages)) {
            self.timelineHasMore = false;
            self.disconnectObserver("_timelineObserver");
          }
        })
        .catch(function (err) {
          self.timelineLoading = false;
          self.toast("toast_error");
          console.error(err);
        });
    },
    appendTimelineItems: function (items) {
      var self = this;
      items.forEach(function (element) {
        var day = element && element.date_create ? String(element.date_create).split(" ")[0] : "";
        if (!day) {
          day = "—";
        }
        if (!self.timelineGroups[day]) {
          self.$set(self.timelineGroups, day, []);
        }
        self.timelineGroups[day].push(element);
      });
    },
    loadLogsPage: function () {
      var self = this;
      if (self.logsLoading || !self.logsHasMore) {
        return;
      }
      var user_id = this.profileUserId();
      if (!user_id) {
        return;
      }
      self.logsLoading = true;
      var next = self.logsPage + 1;
      var url = BASEURL + "api/v1/users/logs/" + user_id + "?page=" + next + "&per_page=20";
      this.fetchJson(url)
        .then(function (response) {
          self.debug ? console.log(url, response) : null;
          self.logsLoading = false;
          self.logsLoaded = true;
          if (!(response && response.code == 200)) {
            self.toast("toast_error");
            return;
          }
          self.logsPage = next;
          var items = Array.isArray(response.data) ? response.data : [];
          self.logs = self.logs.concat(items);
          var totalPages = parseInt(response.total_pages, 10) || 0;
          if (items.length === 0 || (totalPages > 0 && next >= totalPages)) {
            self.logsHasMore = false;
            self.disconnectObserver("_logsObserver");
          }
          self.$nextTick(function () {
            self.observeLogsSentinel();
          });
        })
        .catch(function (err) {
          self.logsLoading = false;
          self.logsLoaded = true;
          self.toast("toast_error");
          console.error(err);
        });
    },
    timelineIcon: function (element) {
      if (!element) {
        return "insert_drive_file";
      }
      if (element.model_type === "page") {
        return "web";
      }
      if (element.model_type === "custom_model") {
        return "view_module";
      }
      if (element.model_type === "custom_model_content") {
        return "view_list";
      }
      return "insert_drive_file";
    },
    timelineIconClass: function (element) {
      var type = element && element.model_type ? element.model_type : "";
      return "user-profile-timeline-item__icon--" + type;
    },
    timelineTypeLabel: function (element) {
      if (!element) {
        return "";
      }
      if (element.model_type === "page") {
        return this.lang("user_profile_type_page");
      }
      if (element.model_type === "custom_model") {
        return this.lang("user_profile_type_collection");
      }
      if (element.model_type === "custom_model_content") {
        return this.lang("user_profile_type_item");
      }
      return element.model_type || "";
    },
    timelineHref: function (element) {
      if (!element) {
        return "";
      }
      if (element.model_type === "page" && element.page_id) {
        return this.base_url("admin/pages/editar/" + element.page_id);
      }
      if (element.model_type === "custom_model" && element.custom_model_id) {
        return this.base_url("admin/custommodels/items/" + element.custom_model_id);
      }
      if (element.model_type === "custom_model_content" && element.custom_model_id) {
        return this.base_url("admin/custommodels/items/" + element.custom_model_id);
      }
      return "";
    },
    isImageFile: function (file) {
      if (!file || !file.file_type) {
        return false;
      }
      var t = String(file.file_type).toLowerCase();
      return ["jpg", "jpeg", "png", "gif", "webp", "svg"].indexOf(t) !== -1;
    },
    fileUrl: function (file) {
      if (!file) {
        return "#";
      }
      var path = String(file.file_path || "").replace(/^\.\//, "");
      return BASEURL + path + file.file_name + "." + file.file_type;
    },
    logChipClass: function (row) {
      var type = row && row.type ? String(row.type) : "";
      if (type === "pages") {
        return "user-profile-chip--interactive";
      }
      if (type === "users") {
        return "user-profile-chip--accent";
      }
      if (type === "custom_model" || type === "custom_model_content") {
        return "user-profile-chip--warning";
      }
      if (type === "config" || type === "site_config") {
        return "user-profile-chip--chrome";
      }
      return "user-profile-chip--neutral";
    },
    logIcon: function (row) {
      var type = row && row.type ? String(row.type) : "";
      if (type === "pages") {
        return "web";
      }
      if (type === "users") {
        return "person";
      }
      if (type === "custom_model") {
        return "view_module";
      }
      if (type === "custom_model_content") {
        return "view_list";
      }
      if (type === "files") {
        return "folder";
      }
      if (type === "config" || type === "site_config") {
        return "settings";
      }
      return "history";
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initTabs: function () {
      var el = document.getElementById("user-tabs");
      if (!el || typeof M === "undefined" || !M.Tabs) {
        return;
      }
      var self = this;
      var inst = M.Tabs.getInstance(el);
      if (inst && typeof inst.destroy === "function") {
        inst.destroy();
      }
      M.Tabs.init(el, {
        onShow: function (content) {
          var id = content && content.id ? content.id : "";
          self.onTabShow(id);
        },
      });
    },
    onTabShow: function (id) {
      this.activeTab = id || this.activeTab;
      if (id === "logs") {
        if (!this.logsLoaded) {
          this.loadLogsPage();
        } else {
          this.observeLogsSentinel();
        }
      }
      if (id === "account") {
        this.accountLoaded = true;
      }
    },
    disconnectObserver: function (key) {
      if (this[key] && this[key].disconnect) {
        this[key].disconnect();
      }
      this[key] = null;
    },
    observeSentinel: function (selector, observerKey, callback) {
      var el = document.querySelector(selector);
      if (!el) {
        return;
      }
      this.disconnectObserver(observerKey);
      if (typeof IntersectionObserver === "undefined") {
        return;
      }
      var self = this;
      var root = el.closest ? el.closest(".user-profile-scroll") : null;
      this[observerKey] = new IntersectionObserver(
        function (entries) {
          var entry = entries && entries[0];
          if (entry && entry.isIntersecting) {
            callback.call(self);
          }
        },
        { root: root || null, rootMargin: "160px", threshold: 0 }
      );
      this[observerKey].observe(el);
    },
    observeTimelineSentinel: function () {
      if (!this.timelineHasMore) {
        this.disconnectObserver("_timelineObserver");
        return;
      }
      this.observeSentinel(".js-timeline-sentinel", "_timelineObserver", this.loadTimelinePage);
    },
    observeLogsSentinel: function () {
      if (!this.logsHasMore) {
        this.disconnectObserver("_logsObserver");
        return;
      }
      this.observeSentinel(".js-logs-sentinel", "_logsObserver", this.loadLogsPage);
    },
    updateAvatar: function () {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/users/avatar",
        data: {
          avatar: self.user.user_data.avatar,
          user_id: self.user.user_id,
        },
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            self.toast("toast_saved");
            return;
          }
          self.toast("toast_error");
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    uploadCallback: function (seletedFiles) {
      if (!seletedFiles || !seletedFiles.length) {
        return;
      }
      var instance = M.Modal.getInstance($(".modal#folderSelector")[0] || $(".modal")[0]);
      if (instance) {
        instance.close();
      }
      var file = new ExplorerFile(seletedFiles[0]);
      this.user.user_data.avatar = file.get_relative_file_path();
      this.updateAvatar();
    },
    setUserStatus: function (status) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/users/status",
        data: {
          user_id: self.user.user_id,
          status: status,
        },
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            self.user.status = status;
            self.toast(status === 1 ? "user_profile_activated" : "user_profile_deactivated");
            self.$nextTick(function () {
              self.initPlugins();
            });
            return;
          }
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    onRequestDelete: function () {
      this.tempDelete(this.user, 0);
      this.$nextTick(function () {
        var el = document.getElementById("deleteModal");
        if (!el || typeof M === "undefined" || !M.Modal) {
          return;
        }
        var inst = M.Modal.getInstance(el);
        if (!inst) {
          inst = M.Modal.init(el, {});
        }
        inst.open();
      });
    },
    confirmCallback: function (data) {
      if (data) {
        this.deleteUser();
      }
    },
    deleteUser: function () {
      var self = this;
      var id = self.user && self.user.user_id;
      if (!id) {
        return;
      }
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/users/" + id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            self.toast("toast_deleted");
            window.location.href = BASEURL + "admin/users";
            return;
          }
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
  },
  mounted: function () {
    this.getUser();
    this.getSummary();
    this.loadTimelinePage();
  },
});
