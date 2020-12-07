var SiteFormNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    siteform_id: null,
    form: {},
    name: "New Form",
    status: false,
    date_publish: "",
    date_create: "",
    date_update: "",
    template: "",
    siteform_items: [],
    items_types: [
      "text",
      "textarea",
      "select",
      "button",
      "checkbox",
      "color",
      "date",
      "datetime-local",
      "email",
      "file",
      "hidden",
      "image",
      "month",
      "number",
      "password",
      "radio",
      "range",
      "reset",
      "search",
      "submit",
      "tel",
      "time",
      "url",
      "week",
    ],
    templates: [],
    positions: {},
    position: "",
    user_id: null,
    user: null,
    realOrder: {},
    group: {},
    targetItem: {},
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      let enable = this.name && this.template ? true : false;
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
        var toastHTML = "<span>Siteform saved </span>";
        M.toast({ html: toastHTML });
        this.setCollapsibleEvent();
      };
      this.loader = true;
      this.runSaveData(callBack);
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/siteforms";
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
            self.siteforms_id = response.data.siteform_id;
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
      return {
        siteform_id: this.siteform_id || "",
        name: this.name || "",
        template: this.template || "",
        status: this.status ? 1 : 0,
        siteform_items: this.siteform_items.map((item) => {
          return {
            ...item,
            properties: JSON.stringify(item.properties),
            data: JSON.stringify(item.data),
          };
        }),
      };
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.siteform_id == value.siteform_id;
      });
    },
    getTemplates() {
      var url = BASEURL + "api/v1/siteforms/templates/";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          this.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            this.templates = response.data.map((template) => {
              return template.split(".")[0];
            });
            this.initPlugins();
            this.setCollapsibleEvent();
          }
        })
        .catch((err) => {
          this.debug ? console.log(err) : null;
          this.loader = false;
        });
    },
    checkEditMode() {
      var self = this;
      if (siteform_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/siteforms/" + siteform_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.siteform_id = response.data.siteform_id;
              self.name = response.data.name;
              self.template = response.data.template;
              self.position = response.data.position;
              self.date_create = response.data.date_create;
              self.date_publish = response.data.date_publish;
              self.status = response.data.status;
              self.user_id = response.data.user_id;
              self.siteform_items = response.data.siteform_items.map((item) => {
                return {
                  ...item,
                  properties: JSON.stringify(item.properties),
                  data: JSON.stringify(item.data),
                };
              });
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
        this.initPlugins();
      }
    },
    addItem() {
      this.siteform_items.push({
        siteform_item_id: "",
        siteform_id: this.siteform_id,
        siteform_item_parent_id: "0",
        item_type: "text",
        item_name: "Field-" + this.makeid(3).toLocaleLowerCase(),
        item_label: "field Text",
        item_title: "Field Title",
        item_placeholder: "Field Title",
        item_placeholder: "Field Title",
        item_class: "",
        date_publish: "",
        date_create: "",
        date_update: "",
        status: "1",
        order: "1",
        data: "{}",
        properties: "{}",
      });
      this.setCollapsibleEvent();
    },
    removeItem(index, items) {
      items.splice(index, 1);
    },
    openPageSelector(item) {
      this.targetItem = item;
      let instance = M.Modal.getInstance($(".modal"));
      instance.open();
    },
    copyCallcack(data) {
      let instance = M.Modal.getInstance($(".modal"));
      instance.close();
      this.targetItem.item_link = "/" + data[0].path;
      this.targetItem.item_name =
        this.string_to_slug(data[0].title) +
        "-" +
        this.makeid(3).toLocaleLowerCase();
      this.targetItem.item_label = data[0].title;
      this.targetItem.item_title = data[0].title;
      this.targetItem.model_id = data[0].page_id;
      this.targetItem.model = "page";
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
            this.setCollapsibleEvent();
            _super($item, container);
          },
        });
      }, 3000);
    },
    setRealOrder() {
      var data = this.group.sortable("serialize").get();
        console.log(data);
        data[0].forEach((element, index) => {
            this.siteform_items = this.siteform_items.map(item => {
                if (item.item_name == element.name) {
                    item.order = index;
                }
                return item;
            })
        });
    },
    getMenuItemsData(data) {
      let mergedData = data.map((element, index) => {
        if (element.subitems && element.subitems[0].length > 0) {
          element.subitems = this.getMenuItemsData(element.subitems[0]);
        }
        let siteform_item = this.getMenuItemByName(
          element.name,
          this.siteform_items
        );
        let merge = {
          ...siteform_item,
          ...element,
          order: index,
        };
        return merge;
      });
      return mergedData;
    },
    getMenuItemByName(name, siteform_items) {
      var siteform_item = null;
      for (let index = 0; index < siteform_items.length; index++) {
        const element = siteform_items[index];
        if (element.item_name == name) {
          siteform_item = element;
          break;
        }
        if (element.subitems.length > 0) {
          let result = this.getMenuItemByName(name, element.subitems);
          if (result && result.item_name == name) {
            siteform_item = result;
            break;
          }
        }
      }
      return siteform_item;
    },
    isEditable(item) {
      return !(item.model && item.model_id);
    },
    makeEditable(item) {
      item.model = null;
      item.model_id = null;
      item.model_object = null;
    },
    removeCollapsideEvent() {
      $(".menuitem .collapsible-header").off();
    },
    setCollapsibleEvent() {
      this.removeCollapsideEvent();
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
    });
  },
});
