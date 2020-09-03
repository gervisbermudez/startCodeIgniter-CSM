jQuery(document).ready(function ($) {
  M.AutoInit();

  $("a.sidenav-trigger-lg").click(function (e) {
    $("body").toggleClass("sidenav-open");
    $("#slide-out").removeAttr("style");
  });

  $("#slide-out").niceScroll();

  window.addEventListener("online", () => {
    // Set hasNetwork to online when they change to online.
    M.toast({ html: "Network detected!" });
  });

  window.addEventListener("offline", () => {
    // Set hasNetwork to offline when they change to offline.
    M.toast({ html: "You are offline!" });
  });
});

// Check that service workers are supported
if ("serviceWorker" in navigator) {
  // Use the window load event to keep the page load performant
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/service-worker.min.js");
  });
}
makeid = function (length) {
  var result = "";
  var characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  var charactersLength = characters.length;
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
};

string_to_slug = function (str) {
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
};

class User {
  user_id = null;
  username = "";
  email;
  lastseen;
  level;
  role;
  status;
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
        this[param] = params[param];
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
