var MenuNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    menu_id: null,
    form: {},
    name: "",
    status: false,
    date_publish: "",
    date_create: "",
    date_update: "",
    template: "",
    menu_items: [],
    templates: [],
    categories_type: [
      "page",
      "formulario",
      "video",
      "foto",
      "evento",
      "contenido",
    ],
    user_id: null,
    user: null,
    realOrder: {},
    group: {},
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      let enable =
        this.name && this.template && this.menu_items.length > 0 ? true : false;
      return enable;
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
    autoSave() {
      if (!this.status) {
        this.runSaveData();
        this.debug ? console.log("running autosave...") : null;
      }
    },
    validateField(field) {
      let self = UserNewForm;
      if (self.form.validateField(field)) {
        self.serverValidation(field);
        return;
      }
      return self.form.fields[field].valid;
    },
    validateForm() {
      this.form.validate();
      let errors = true;
      return this.form.errors.length == 0 && errors;
    },
    save() {
      var self = this;
      var callBack = (response) => {
        var toastHTML = "<span>Menu saved </span>";
        M.toast({ html: toastHTML });
      };
      this.loader = true;
      this.runSaveData(callBack);
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/menus";
      $.ajax({
        type: "POST",
        url: url,
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          setTimeout(() => {
            self.loader = false;
          }, 1500);
          if (response.code == 200) {
            self.editMode = true;
            self.menu_id = response.data.menu_id;
            self.menu_items = response.data.menu_items;
            if (typeof callBack == "function") {
              callBack(response);
            }
          } else {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          }
        },
        error: function (response) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    getData: function () {
      this.setRealOrder();
      return {
        menu_id: this.menu_id || "",
        name: this.name || "",
        template: this.template || "",
        status: this.status ? 1 : 0,
        menu_items: this.realOrder,
      };
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.menu_id == value.menu_id;
      });
    },
    getTemplates() {
      var url = BASEURL + "api/v1/menus/templates/";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          this.loader = false;
          this.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            this.templates = response.data.map((template) => {
              return template.split(".")[0];
            });
            this.initPlugins();
          }
        })
        .catch((err) => {
          this.debug ? console.log(err) : null;
          this.loader = false;
        });
    },
    checkEditMode() {
      var self = this;
      if (menu_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/menus/" + menu_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.menu_id = response.data.menu_id;
              self.name = response.data.name;
              self.template = response.data.template;
              self.date_create = response.data.date_create;
              self.date_publish = response.data.date_publish;
              self.status = response.data.status;
              self.user_id = response.data.user_id;
              self.menu_items = response.data.menu_items;
              self.user = new User(response.data.user);
            }
            this.initPlugins();
          })
          .catch((response) => {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          });
      } else {
        self.loader = false;
      }
    },
    addItem() {
      this.menu_items.push({
        menu_item_id: "",
        menu_id: this.menu_id,
        menu_item_parent_id: "0",
        item_type: "static_link",
        item_name: "link-" + this.makeid(3).toLocaleLowerCase(),
        item_label: "Link Text",
        item_link: "#!",
        item_title: "Link Title",
        item_target: "_self",
        date_publish: "",
        date_create: "",
        date_update: "",
        status: "1",
        subitems: [],
      });
      this.removeCollapsideEvent();
      this.setCollapsibleEvent();
    },
    removeItem(index) {
      this.menu_items.splice(index, 1);
    },
    initPlugins() {
      setTimeout(() => {
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
        this.group = $(".default").sortable({
          delay: 500,
          handle: "i.icon-move",
          onDrop: ($item, container, _super) => {
            this.setRealOrder();
            _super($item, container);
          },
        });
      }, 3000);
    },
    setRealOrder() {
      var data = this.group.sortable("serialize").get();
      var strData = JSON.stringify(data);
      var strData = strData.replace(/children/g, "subitems");
      data = JSON.parse(strData);
      if (data) {
        let result = this.getMenuItemsData(data[0]);
        this.realOrder = result;
      }
    },
    getMenuItemsData(data) {
      let mergedData = data.map((element, index) => {
        if (element.subitems && element.subitems[0].length > 0) {
          element.subitems = this.getMenuItemsData(element.subitems[0]);
        }
        let menu_item = this.getMenuItemByName(element.name, this.menu_items);
        let merge = {
          ...menu_item,
          ...element,
          order: index,
        };
        return merge;
      });
      return mergedData;
    },
    getMenuItemByName(name, menu_items) {
      var menu_item = null;
      for (let index = 0; index < menu_items.length; index++) {
        const element = menu_items[index];
        if (element.item_name == name) {
          menu_item = element;
          break;
        }
        if (element.subitems.length > 0) {
          let result = this.getMenuItemByName(name, element.subitems);
          if (result && result.item_name == name) {
            menu_item = result;
            break;
          }
        }
      }
      return menu_item;
    },
    removeCollapsideEvent() {
      $(".menuitem .collapsible-header").off();
    },
    setCollapsibleEvent() {
      setTimeout(() => {
        $(".menuitem .collapsible-header").click(function (element) {
          element.preventDefault();
          let $element = $(this);
          let $parent = $element.parent();
          $parent.toggleClass("active");
        });
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted MenuNewForm") : null;
      this.getTemplates();
      this.checkEditMode();
      this.setCollapsibleEvent();
    });
  },
});
