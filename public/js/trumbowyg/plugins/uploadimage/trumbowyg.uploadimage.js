(function ($) {
  "use strict";

  // Plugin default options
  var defaultOptions = {};

  // If the plugin is a button
  function buildButtonDef(trumbowyg) {
    return {
      fn: function () {
        // Plugin button logic
        let instance = M.Modal.getInstance($("#editorModal"));
        instance.open();
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
      },
    };
  }

  $.extend(true, $.trumbowyg, {
    // Add some translations
    langs: {
      en: {
        uploadimage: "Upload Image",
      },
    },
    // Register plugin in Trumbowyg
    plugins: {
      uploadimage: {
        // Code called by Trumbowyg core to register the plugin
        init: function (trumbowyg) {
          trumbowygInstance = trumbowyg;
          // Fill current Trumbowyg instance with the plugin default options
          trumbowyg.o.plugins.uploadimage = $.extend(
            true,
            {},
            defaultOptions,
            trumbowyg.o.plugins.uploadimage || {}
          );

          // If the plugin is a paste handler, register it
          trumbowyg.pasteHandlers.push(function (pasteEvent) {
            // My plugin paste logic
          });

          // If the plugin is a button
          trumbowyg.addBtnDef("uploadimage", buildButtonDef(trumbowyg));
        },
        // Return a list of button names which are active on current element
        tagHandler: function (element, trumbowyg) {
          return [];
        },
        destroy: function (trumbowyg) {},
      },
    },
  });
})(jQuery);
