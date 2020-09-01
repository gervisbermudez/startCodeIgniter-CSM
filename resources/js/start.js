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
