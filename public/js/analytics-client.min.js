/**
 * Analytics Tracking Client
 *
 * First-party events → POST /api/v1/analytics/event
 * Independent of Google Analytics (gtag).
 */
(function () {
  "use strict";

  function analyticsBase() {
    if (typeof BASEURL !== "undefined" && BASEURL) {
      return BASEURL.replace(/\/?$/, "/");
    }
    return "/";
  }

  var CONFIG = {
    apiEndpoint: analyticsBase() + "api/v1/analytics",
    autoTrack: true,
    trackClicks: true,
    trackScroll: true,
    trackFormSubmits: true,
    trackPageTime: true,
    sendInterval: 5000,
  };

  var pageStartTime = Date.now();
  var eventQueue = [];
  var sendTimer = null;
  var maxScrollDepth = 0;
  var isPageVisible = true;

  window.trackEvent = function (category, action, label, value, metadata) {
    var event = {
      category: category,
      action: action,
      label: label || null,
      value: value || null,
      metadata: metadata || null,
      timestamp: new Date().toISOString(),
    };

    eventQueue.push(event);

    if (category === "Conversion" || category === "Error") {
      sendEvents();
    } else {
      scheduleSend();
    }
  };

  window.trackConversion = function () {
    fetch(CONFIG.apiEndpoint + "/conversion", {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({}),
    }).catch(function () {});
  };

  function sendEvents() {
    if (eventQueue.length === 0) {
      return;
    }

    var events = eventQueue.slice();
    eventQueue = [];

    events.forEach(function (event) {
      fetch(CONFIG.apiEndpoint + "/event", {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(event),
      }).catch(function () {});
    });
  }

  function scheduleSend() {
    if (sendTimer) {
      clearTimeout(sendTimer);
    }
    sendTimer = setTimeout(sendEvents, CONFIG.sendInterval);
  }

  function setupClickTracking() {
    if (!CONFIG.trackClicks) {
      return;
    }

    document.addEventListener("click", function (e) {
      var target = e.target.closest("a, button, [data-track]");
      if (!target) {
        return;
      }

      var label =
        target.getAttribute("data-track-label") ||
        target.textContent.trim() ||
        target.getAttribute("href") ||
        target.getAttribute("id") ||
        target.className;

      trackEvent("Click", target.tagName, label);
    });
  }

  function setupScrollTracking() {
    if (!CONFIG.trackScroll) {
      return;
    }

    var scrollTimeout;

    window.addEventListener("scroll", function () {
      clearTimeout(scrollTimeout);

      scrollTimeout = setTimeout(function () {
        var denom = document.documentElement.scrollHeight - window.innerHeight;
        var scrollPercent = denom > 0 ? Math.round((window.scrollY / denom) * 100) : 0;

        if (scrollPercent > maxScrollDepth) {
          maxScrollDepth = scrollPercent;

          if (
            scrollPercent === 25 ||
            scrollPercent === 50 ||
            scrollPercent === 75 ||
            scrollPercent === 100
          ) {
            trackEvent("Scroll", "Depth", scrollPercent + "%", scrollPercent);
          }
        }
      }, 150);
    });
  }

  function setupFormTracking() {
    if (!CONFIG.trackFormSubmits) {
      return;
    }

    document.addEventListener("submit", function (e) {
      var form = e.target;
      var formName = form.getAttribute("name") || form.getAttribute("id") || "unnamed-form";
      trackEvent("Form", "Submit", formName);
    });
  }

  function setupTimeTracking() {
    if (!CONFIG.trackPageTime) {
      return;
    }

    setInterval(function () {
      if (isPageVisible) {
        var timeOnPage = Math.round((Date.now() - pageStartTime) / 1000);
        if (timeOnPage % 30 === 0) {
          trackEvent("Engagement", "Time on Page", window.location.pathname, timeOnPage);
        }
      }
    }, 30000);
  }

  function setupVisibilityTracking() {
    document.addEventListener("visibilitychange", function () {
      if (document.hidden) {
        isPageVisible = false;
        trackEvent("Engagement", "Page Hidden", window.location.pathname);
      } else {
        isPageVisible = true;
        trackEvent("Engagement", "Page Visible", window.location.pathname);
      }
    });
  }

  function setupOutboundTracking() {
    document.addEventListener("click", function (e) {
      var link = e.target.closest("a");
      if (!link) {
        return;
      }

      var href = link.getAttribute("href");
      if (!href) {
        return;
      }

      if (href.indexOf("http") === 0 && href.indexOf(window.location.hostname) === -1) {
        trackEvent("Outbound", "Click", href);
      }
    });
  }

  function setupDownloadTracking() {
    var downloadExtensions = [".pdf", ".zip", ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx"];

    document.addEventListener("click", function (e) {
      var link = e.target.closest("a");
      if (!link) {
        return;
      }

      var href = link.getAttribute("href");
      if (!href) {
        return;
      }

      var extension = href.substring(href.lastIndexOf(".")).toLowerCase();
      if (downloadExtensions.indexOf(extension) !== -1) {
        trackEvent("Download", extension, href);
      }
    });
  }

  function sendScreenResolution() {
    var resolution = screen.width + "x" + screen.height;
    trackEvent("Device", "Screen Resolution", resolution);
  }

  function setupErrorTracking() {
    window.addEventListener("error", function (e) {
      trackEvent("Error", "JavaScript Error", e.message, null, {
        filename: e.filename,
        lineno: e.lineno,
        colno: e.colno,
      });
    });

    window.addEventListener("unhandledrejection", function (e) {
      trackEvent(
        "Error",
        "Unhandled Promise Rejection",
        e.reason ? e.reason.toString() : "Unknown",
        null,
        { reason: e.reason ? String(e.reason) : null }
      );
    });
  }

  function setupUnloadTracking() {
    window.addEventListener("beforeunload", function () {
      var timeOnPage = Math.round((Date.now() - pageStartTime) / 1000);
      var data = JSON.stringify({
        category: "Engagement",
        action: "Page Exit",
        label: window.location.pathname,
        value: timeOnPage,
        metadata: { scroll_depth: maxScrollDepth },
      });

      if (navigator.sendBeacon) {
        try {
          var blob = new Blob([data], { type: "application/json" });
          navigator.sendBeacon(CONFIG.apiEndpoint + "/event", blob);
        } catch (err) {
          navigator.sendBeacon(CONFIG.apiEndpoint + "/event", data);
        }
      }
    });
  }

  function init() {
    if (!CONFIG.autoTrack) {
      return;
    }

    sendScreenResolution();
    setupClickTracking();
    setupScrollTracking();
    setupFormTracking();
    setupTimeTracking();
    setupVisibilityTracking();
    setupOutboundTracking();
    setupDownloadTracking();
    setupErrorTracking();
    setupUnloadTracking();

    trackEvent("Pageview", window.location.pathname, document.title);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  window.AnalyticsConfig = CONFIG;
})();
