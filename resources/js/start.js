jQuery(document).ready(function ($) {
  M.AutoInit();

  $("a.sidenav-trigger-lg").click(function (e) {
    $("body").toggleClass("sidenav-open");
    $("#slide-out").removeAttr("style");
  });

  $(".sidenav").niceScroll();
});

var mixins = {
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  methods: {
    base_url: function (path) {
      return BASEURL + path;
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
    try {
      return this.user_data.nombre + " " + this.user_data.apellido;
    } catch (error) {
      return "";
    }
  };

  get_profileurl = () => {
    return BASEURL + "admin/usuarios/ver/" + this.user_id;
  };

  get_avatarurl = () => {
    try {
      return (
        BASEURL +
        "/public/img/profile/" +
        this.username +
        "/" +
        this.user_data.avatar
      );
    } catch (error) {
      return "";
    }
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
    return "https://materializecss.com/images/sample-1.jpg";
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
    try {
      return this.file_name + "." + this.file_type;
    } catch (error) {
      return "";
    }
  };

  getFullFilePath = () => {
    return BASEURL + this.file_path + this.get_filename();
  };

  getFullSharePath = () => {
    return BASEURL + this.share_link;
  };
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

if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/service-worker.min.js", {
      scope: "/admin",
    });
  });
}
