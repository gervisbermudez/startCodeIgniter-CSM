jQuery(document).ready(function ($) {
  M.AutoInit();

  $("a.sidenav-trigger-lg").click(function (e) {
    $("body").toggleClass("sidenav-open");
    $("#slide-out").removeAttr("style");
  });

  $(".sidenav").niceScroll();

  $("#darkmode-switch").change(function (e) {
    e.preventDefault();
    $("html").toggleClass("dark-mode");
    if ($(this).is(":checked")) {
      localStorage.setItem("dark-mode", "dark-mode");
    } else {
      localStorage.removeItem("dark-mode", "dark-mode");
    }
  });

  $("#darkmode-switch").prop("checked", false);

  if (localStorage.getItem("dark-mode")) {
    $("html").toggleClass("dark-mode");
    $("#darkmode-switch").prop("checked", true);
  }
});

var mixins = {
  data() {
    return {
      debug: DEBUGMODE,
      orderDataConf: {
        strPropertyName: null,
        sort_as: "asc",
      },
      toDeleteItem: {}
    }
  },
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  methods: {
    searchInObject(object, strSearchTerm) {
      var keys = Object.keys(object);
      var result = false;
      for (var i = 0; i < keys.length; i++) {
        if (typeof object[keys[i]] == "string") {
          item_val = object[keys[i]];
          result = item_val.toLowerCase().indexOf(strSearchTerm) != -1;
          if (result) {
            break;
          }
        }
      }
      return result;
    },
    getFullFileName(file) {
      return file.file_name + "." + file.file_type;
    },
    getFullFilePath(file) {
      return BASEURL + file.file_path + this.getFullFileName(file);
    },
    getSortData(strPropertyName) {
      if (this.orderDataConf.strPropertyName == strPropertyName) {
        return "sort_desc";
      }
      if (this.orderDataConf.strPropertyName == "-" + strPropertyName) {
        return "sort_asc";
      }
      return "both";
    },
    sortData(strPropertyName, array) {
      strPropertyName =
        this.orderDataConf.strPropertyName == null
          ? strPropertyName
          : this.orderDataConf.strPropertyName == strPropertyName
          ? "-" + strPropertyName
          : strPropertyName;
      let sorted = array.sort(this.dynamicSort(strPropertyName));
      this.orderDataConf.strPropertyName = strPropertyName;
      array = sorted;
    },
    dynamicSort(property) {
      var sortOrder = 1;
      if (property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
      }
      return function (a, b) {
        /* next line works with strings and numbers,
         * and you may want to customize it to your needs
         */
        var result =
          a[property] < b[property] ? -1 : a[property] > b[property] ? 1 : 0;
        return result * sortOrder;
      };
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    getcontentText: function (html, length = 120) {
      var span = document.createElement("span");
      span.innerHTML = html;
      let text = span.textContent || span.innerText;
      return text.substring(0, length) + "...";
    },
    makeid: function (length) {
      var result = "";
      var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      var charactersLength = characters.length;
      for (var i = 0; i < length; i++) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
      }
      return result;
    },
    string_to_slug: function (str) {
      if (str.length == 0) return "";

      str = str.replace(/^\s+|\s+$/g, ""); // trim
      str = str.toLowerCase();

      // remove accents, swap ñ for n, etc
      var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
      var to = "aaaaeeeeiiiioooouuuunc------";
      for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
      }

      str = str
        .replace(/[^a-z0-9 -]/g, "") // remove invalid chars
        .replace(/\s+/g, "-") // collapse whitespace and replace by -
        .replace(/-+/g, "-"); // collapse dashes

      return str;
    },
    getFormattedDate: function (
      date,
      prefomattedDate = false,
      hideYear = false
    ) {
      const MONTH_NAMES = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      const day = date.getDate();
      const month = MONTH_NAMES[date.getMonth()];
      const year = date.getFullYear();
      const hours = date.getHours();
      let minutes = date.getMinutes();

      if (minutes < 10) {
        // Adding leading zero to minutes
        minutes = `0${minutes}`;
      }

      if (prefomattedDate) {
        // Today at 10:20
        // Yesterday at 10:20
        return `${prefomattedDate} at ${hours}:${minutes}`;
      }

      if (hideYear) {
        // 10. January at 10:20
        return `${day} ${month} at ${hours}:${minutes}`;
      }

      // 10. January 2017. at 10:20
      return `${day} ${month} ${year} at ${hours}:${minutes}`;
    },
    timeAgo: function (dateParam) {
      if (!dateParam) {
        return null;
      }

      const date =
        typeof dateParam === "object" ? dateParam : new Date(dateParam);
      const DAY_IN_MS = 86400000; // 24 * 60 * 60 * 1000
      const today = new Date();
      const yesterday = new Date(today - DAY_IN_MS);
      const seconds = Math.round((today - date) / 1000);
      const minutes = Math.round(seconds / 60);
      const isToday = today.toDateString() === date.toDateString();
      const isYesterday = yesterday.toDateString() === date.toDateString();
      const isThisYear = today.getFullYear() === date.getFullYear();

      if (seconds < 5) {
        return "now";
      } else if (seconds < 60) {
        return `${seconds} seconds ago`;
      } else if (seconds < 90) {
        return "about a minute ago";
      } else if (minutes < 60) {
        return `${minutes} minutes ago`;
      } else if (isToday) {
        return this.getFormattedDate(date, "Today"); // Today at 10:20
      } else if (isYesterday) {
        return this.getFormattedDate(date, "Yesterday"); // Yesterday at 10:20
      } else if (isThisYear) {
        return this.getFormattedDate(date, false, true); // 10. January at 10:20
      }

      return this.getFormattedDate(date); // 10. January 2017. at 10:20
    },
  },
};

formsElements = [
  {
    field_name: "title",
    displayName: "Titulo",
    icon: "format_color_text",
    component: "formFieldTitle",
    status: "1",
    data: {},
  },
  {
    field_name: "text",
    displayName: "Texto",
    icon: "short_text",
    component: "formFieldTextArea",
    status: "1",
    data: {},
  },
  {
    field_name: "formatText",
    displayName: "Texto con formato",
    component: "formTextFormat",
    icon: "format_size",
    status: "1",
    data: {},
  },
  {
    field_name: "image",
    displayName: "Imagen",
    component: "formImageSelector",
    icon: "image",
    status: "1",
    data: {},
  },
  {
    field_name: "date",
    displayName: "Fecha",
    component: "formFieldDate",
    icon: "date_range",
    status: "1",
    data: {},
  },
  {
    field_name: "time",
    displayName: "Hora",
    component: "formFieldTime",
    icon: "access_time",
    status: "1",
    data: {},
  },
  {
    field_name: "number",
    displayName: "Numero",
    component: "formFieldNumber",
    icon: "looks_one",
    status: "1",
    data: {},
  },
  {
    field_name: "dropdown_select",
    displayName: "Select",
    component: "formFieldSelect",
    status: "1",
    icon: "list",
    data: {},
  },
  {
    field_name: "bolean",
    displayName: "Bolean",
    component: "formFieldBoolean",
    status: "1",
    icon: "check_circle",
    data: {},
  },
];

class User {
  user_id = null;
  username = "";
  email = "";
  lastseen = "";
  level = "";
  role = "";
  status = "";
  usergroup_id;
  user_data = {
    nombre: "",
    apellido: "",
    direccion: "",
    telefono: "",
    "create by": "",
  };

  constructor(params) {
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param] || "";
      }
    }
  }

  get_fullname = () => {
    if (this.user_data.nombre && this.user_data.apellido) {
      return this.user_data.nombre + " " + this.user_data.apellido;
    } else {
      return "";
    }
  };

  get_profileurl = () => {
    return BASEURL + "admin/usuarios/ver/" + this.user_id;
  };

  get_avatarurl = () => {
    if (this.user_data.avatar) {
      return BASEURL + this.user_data.avatar;
    } else {
      return BASEURL + "public/img/profile/default.png";
    }
  };

  get_edit_url = () => {
    return BASEURL + "admin/usuarios/edit/" + this.user_id;
  };
}

class Page {
  page_id = null;
  categorie_id = "";
  content = "";
  date_create = "";
  date_publish = "";
  date_update = "";
  layout = "";
  mainImage = null;
  model_type = "";
  page_type_id = "";
  path = "";
  status = "";
  subcategorie_id = "";
  subtitle = "";
  template = "";
  title = "";
  user = new User();
  user_id = "";
  visibility = "";

  constructor(params) {
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param] || "";
      }
    }
  }

  getcontentText = function () {
    var span = document.createElement("span");
    span.innerHTML = this.content;
    let text = span.textContent || span.innerText;
    return text.substring(0, 220) + "...";
  };

  getPageImagePath() {
    if (this.imagen_file) {
      return (
        BASEURL +
        this.imagen_file.file_path.substr(2) +
        this.imagen_file.file_name +
        "." +
        this.imagen_file.file_type
      );
    }
    return BASEURL + "public/img/default.jpg";
  }

  getPageFullPath = function () {
    if (this.status == 1) {
      return BASEURL + this.path;
    }
    return BASEURL + "admin/paginas/editar/" + this.page_id;
  };
}

class ExplorerFile {
  date_create = "";
  date_update = "";
  featured = "";
  file_id = "";
  file_name = "";
  file_path = "";
  file_type = "";
  parent_name = "";
  rand_key = "";
  share_link = "";
  shared_user_group_id = "";
  status = "";
  user_id = "";
  user = new User();

  constructor(params) {
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param] || "";
      }
    }
  }

  get_filename = () => {
    return this.file_name + "." + this.file_type;
  };

  get_relative_file_path = () => {
    return this.file_path + this.get_filename();
  };

  get_full_file_path = () => {
    return BASEURL + this.file_path + this.get_filename();
  };

  get_full_share_path = () => {
    return BASEURL + this.share_link;
  };

  get_icon() {
    let icon = "far fa-file";
    switch (this.file_type) {
      case "folder":
        icon = "far fa-folder";
        break;
      case "jpg":
      case "png":
      case "gif":
        icon = "fas fa-file-image";
        break;
      case "html":
        icon = "fab fa-html5";
        break;
      case "scss":
        icon = "fab fa-sass";
        break;
      case "css":
      case "min.css":
        icon = "fab fa-css3-alt";
        break;
      case "txt":
        icon = "far fa-file-alt";
        break;
      case "php":
      case "blade.php":
        icon = "fab fa-php";
        break;
      case "js":
      case "json":
      case "min.js":
        icon = "fab fa-js";
        break;
      case "eot":
      case "otf":
      case "woff2":
        icon = "fas fa-font";
        break;
    }
    return icon;
  }

  is_image() {
    if (
      this.file_type == "jpg" ||
      this.file_type == "png" ||
      this.file_type == "gif"
    ) {
      return true;
    }
    return false;
  }
}

class Config_data {
  type_value = "string";
  validate_as = "text";
  max_lenght = "120";
  min_lenght = "0";
  handle_as = "input";
  input_type = "text";
  perm_values = null;

  constructor(params) {
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param];
      }
    }

    switch (this.type_value) {
      case "boolean":
        this.handle_as = "switch";
        break;

      case "number":
        this.handle_as = "input";
        this.input_type = "number";
        break;

      default:
        break;
    }
  }
}

if ("serviceWorker" in navigator && window.location.protocol == "https:") {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/service-worker.min.js", {
      scope: "/admin",
    });
  });
}

Vue.component("userInfo", {
  template: `
  <div class="collection form-user-component user-info-inline">
    <div class="collection-item avatar">
      <a :href="user.get_profileurl()">
        <img :src="user.get_avatarurl()" alt="" class="circle profile-img">
        <span class="title">{{user.get_fullname()}}</span>
        <div>{{user.usergroup.name}}</div>
      </a>
    </div>
  </div>
  `,
  props: ["user"],
});


Vue.component("preloader", {
  template: `
  <div class="preloader-wrapper big active">
      <div class="spinner-layer spinner-blue-only">
          <div class="circle-clipper left">
              <div class="circle"></div>
          </div>
          <div class="gap-patch">
              <div class="circle"></div>
          </div>
          <div class="circle-clipper right">
              <div class="circle"></div>
          </div>
      </div>
  </div>
  `,
});

Vue.component("confirmModal", {
  props: ["id", "title"],
  template: `
    <div :id="id" class="modal confirm-modal">
    <div class="modal-content">
          <div class="modal-header">
            <div class="row">
              <div class="col s12">
                <h4>{{title}}</h4>
              </div>
            </div>
          </div>
          <div class="row">
              <div class="col s12">
                  <slot></slot>
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <a href="#!" class="modal-action modal-close waves-effect waves-red btn red" @click="onClickButton(false);">Cancelar</a>
          <a href="#!" class="modal-close waves-effect waves-green btn" @click="onClickButton(true);">Aceptar</a>
      </div>
  </div>
  `,
  methods: {
    onClickButton(result) {
      this.$emit("notify", result);
    },
  },
});

Vue.component("preview", {
  props: ["url"],
  data() {
    return {
      fullScreen: false,
      previewUrl: ''
    }
  },
  template: `
  <div class="preview-container" v-bind:class="{fixed: fullScreen}">
    <div class="preview-options">
    <input type="text" name="url" id="url" v-model="previewUrl">
    <a href="#!" class="option"><i class="material-icons" v-on:click="expand();">aspect_ratio</i></a>
    </div>
    <iframe class="responsive-iframe" :src="previewUrl"></iframe>
  </div>
  `,
  watch: {
    url: function (val) {
      this.previewUrl = val;
    }
  },
  mounted() {
    this.$nextTick(function () {
      this.previewUrl = this.url;
    });
  },
  methods: {
    togglePreview() {
      this.fullScreen = !this.fullScreen;
    },
    expand() {
      this.$emit("expand");
    },
  },
});
