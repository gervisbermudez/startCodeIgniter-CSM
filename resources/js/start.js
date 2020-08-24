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
