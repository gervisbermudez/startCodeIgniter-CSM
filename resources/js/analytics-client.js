/**
 * Analytics Tracking Client
 * 
 * Cliente JavaScript para tracking de eventos y métricas
 * en el frontend
 */

(function() {
  'use strict';

  // Configuración
  const CONFIG = {
    apiEndpoint: '/api/v1/analytics',
    autoTrack: true,
    trackClicks: true,
    trackScroll: true,
    trackFormSubmits: true,
    trackPageTime: true,
    sendInterval: 5000, // 5 segundos
  };

  // Estado
  let pageStartTime = Date.now();
  let eventQueue = [];
  let sendTimer = null;
  let maxScrollDepth = 0;
  let isPageVisible = true;

  /**
   * Track custom event
   */
  window.trackEvent = function(category, action, label, value, metadata) {
    const event = {
      category: category,
      action: action,
      label: label || null,
      value: value || null,
      metadata: metadata || null,
      timestamp: new Date().toISOString()
    };

    eventQueue.push(event);
    
    // Enviar inmediatamente si es un evento importante
    if (category === 'Conversion' || category === 'Error') {
      sendEvents();
    } else {
      scheduleSend();
    }
  };

  /**
   * Track conversion
   */
  window.trackConversion = function() {
    fetch(CONFIG.apiEndpoint + '/conversion', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({})
    }).catch(err => console.error('Error tracking conversion:', err));
  };

  /**
   * Send queued events to server
   */
  function sendEvents() {
    if (eventQueue.length === 0) return;

    const events = [...eventQueue];
    eventQueue = [];

    // Enviar cada evento
    events.forEach(event => {
      fetch(CONFIG.apiEndpoint + '/event', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(event)
      }).catch(err => console.error('Error sending event:', err));
    });
  }

  /**
   * Schedule event sending
   */
  function scheduleSend() {
    if (sendTimer) {
      clearTimeout(sendTimer);
    }
    sendTimer = setTimeout(sendEvents, CONFIG.sendInterval);
  }

  /**
   * Track clicks
   */
  function setupClickTracking() {
    if (!CONFIG.trackClicks) return;

    document.addEventListener('click', function(e) {
      const target = e.target.closest('a, button, [data-track]');
      if (!target) return;

      const label = target.getAttribute('data-track-label') || 
                   target.textContent.trim() ||
                   target.getAttribute('href') ||
                   target.getAttribute('id') ||
                   target.className;

      trackEvent('Click', target.tagName, label);
    });
  }

  /**
   * Track scroll depth
   */
  function setupScrollTracking() {
    if (!CONFIG.trackScroll) return;

    let scrollTimeout;
    
    window.addEventListener('scroll', function() {
      clearTimeout(scrollTimeout);
      
      scrollTimeout = setTimeout(function() {
        const scrollPercent = Math.round(
          (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
        );

        if (scrollPercent > maxScrollDepth) {
          maxScrollDepth = scrollPercent;

          // Track milestones
          if (scrollPercent === 25 || scrollPercent === 50 || 
              scrollPercent === 75 || scrollPercent === 100) {
            trackEvent('Scroll', 'Depth', scrollPercent + '%', scrollPercent);
          }
        }
      }, 150);
    });
  }

  /**
   * Track form submissions
   */
  function setupFormTracking() {
    if (!CONFIG.trackFormSubmits) return;

    document.addEventListener('submit', function(e) {
      const form = e.target;
      const formName = form.getAttribute('name') || 
                      form.getAttribute('id') || 
                      'unnamed-form';

      trackEvent('Form', 'Submit', formName);
    });
  }

  /**
   * Track time on page
   */
  function setupTimeTracking() {
    if (!CONFIG.trackPageTime) return;

    // Track every 30 seconds if page is visible
    setInterval(function() {
      if (isPageVisible) {
        const timeOnPage = Math.round((Date.now() - pageStartTime) / 1000);
        
        // Solo trackear cada 30 segundos
        if (timeOnPage % 30 === 0) {
          trackEvent('Engagement', 'Time on Page', window.location.pathname, timeOnPage);
        }
      }
    }, 30000);
  }

  /**
   * Track page visibility
   */
  function setupVisibilityTracking() {
    document.addEventListener('visibilitychange', function() {
      if (document.hidden) {
        isPageVisible = false;
        trackEvent('Engagement', 'Page Hidden', window.location.pathname);
      } else {
        isPageVisible = true;
        trackEvent('Engagement', 'Page Visible', window.location.pathname);
      }
    });
  }

  /**
   * Track outbound links
   */
  function setupOutboundTracking() {
    document.addEventListener('click', function(e) {
      const link = e.target.closest('a');
      if (!link) return;

      const href = link.getAttribute('href');
      if (!href) return;

      // Check if external link
      if (href.startsWith('http') && !href.includes(window.location.hostname)) {
        trackEvent('Outbound', 'Click', href);
      }
    });
  }

  /**
   * Track downloads
   */
  function setupDownloadTracking() {
    const downloadExtensions = ['.pdf', '.zip', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx'];

    document.addEventListener('click', function(e) {
      const link = e.target.closest('a');
      if (!link) return;

      const href = link.getAttribute('href');
      if (!href) return;

      const extension = href.substring(href.lastIndexOf('.')).toLowerCase();
      if (downloadExtensions.includes(extension)) {
        trackEvent('Download', extension, href);
      }
    });
  }

  /**
   * Send screen resolution
   */
  function sendScreenResolution() {
    const resolution = screen.width + 'x' + screen.height;
    // Enviar como evento, no modificar la URL
    trackEvent('Device', 'Screen Resolution', resolution);
  }

  /**
   * Track JavaScript errors
   */
  function setupErrorTracking() {
    window.addEventListener('error', function(e) {
      trackEvent(
        'Error',
        'JavaScript Error',
        e.message,
        null,
        {
          filename: e.filename,
          lineno: e.lineno,
          colno: e.colno
        }
      );
    });

    window.addEventListener('unhandledrejection', function(e) {
      trackEvent(
        'Error',
        'Unhandled Promise Rejection',
        e.reason ? e.reason.toString() : 'Unknown',
        null,
        {
          reason: e.reason
        }
      );
    });
  }

  /**
   * Track page unload
   */
  function setupUnloadTracking() {
    window.addEventListener('beforeunload', function() {
      const timeOnPage = Math.round((Date.now() - pageStartTime) / 1000);
      
      // Usar sendBeacon para garantizar que el evento se envíe
      const data = JSON.stringify({
        category: 'Engagement',
        action: 'Page Exit',
        label: window.location.pathname,
        value: timeOnPage,
        metadata: {
          scroll_depth: maxScrollDepth
        }
      });

      if (navigator.sendBeacon) {
        navigator.sendBeacon(CONFIG.apiEndpoint + '/event', data);
      }
    });
  }

  /**
   * Initialize tracking
   */
  function init() {
    if (!CONFIG.autoTrack) return;

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

    // Track page view
    trackEvent('Pageview', window.location.pathname, document.title);
  }

  // Auto-initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose configuration
  window.AnalyticsConfig = CONFIG;

})();

/**
 * Ejemplo de uso:
 * 
 * // Track evento personalizado
 * trackEvent('Button', 'Click', 'Subscribe Button', 1);
 * 
 * // Track conversión
 * trackConversion();
 * 
 * // Track evento con metadata
 * trackEvent('Video', 'Play', 'Intro Video', null, {
 *   duration: 120,
 *   quality: '1080p'
 * });
 * 
 * // Deshabilitar auto-tracking
 * AnalyticsConfig.autoTrack = false;
 * 
 * // Track manual
 * trackEvent('Custom', 'Action', 'Label');
 */
