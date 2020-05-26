importScripts(
  "https://storage.googleapis.com/workbox-cdn/releases/4.3.1/workbox-sw.js"
);

if (workbox) {
  console.log(`Yay! Workbox is loaded 🎉`);

  // cache name
  workbox.core.setCacheNameDetails({
    prefix: "My-awesome-cache",
    precache: "precache",
    runtime: "runtime",
  });
  //Google Fonts
  // Cache the Google Fonts stylesheets with a stale-while-revalidate strategy.
  workbox.routing.registerRoute(
    /^https:\/\/fonts\.googleapis\.com/,
    new workbox.strategies.StaleWhileRevalidate({
      cacheName: "google-fonts-stylesheets",
    })
  );

  // Cache the underlying font files with a cache-first strategy for 1 year.
  workbox.routing.registerRoute(
    /^https:\/\/fonts\.gstatic\.com/,
    new workbox.strategies.CacheFirst({
      cacheName: "google-fonts-webfonts",
      plugins: [
        new workbox.cacheableResponse.Plugin({
          statuses: [0, 200],
        }),
        new workbox.expiration.Plugin({
          maxAgeSeconds: 60 * 60 * 24 * 365,
          maxEntries: 30,
        }),
      ],
    })
  );
  // runtime cache
  // 1. stylesheet
  workbox.routing.registerRoute(
    /\.(?:js|css)$/,
    new workbox.strategies.StaleWhileRevalidate({
      cacheName: "static-resources",
    })
  );

  // 2. images
  workbox.routing.registerRoute(
    /\.(?:png|gif|jpg|jpeg|webp|svg)$/,
    new workbox.strategies.CacheFirst({
      cacheName: "images",
      plugins: [
        new workbox.expiration.Plugin({
          maxEntries: 60,
          maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Days
        }),
      ],
    })
  );

  //Caching Content from Multiple Origins
  workbox.routing.registerRoute(
    /.*(?:googleapis|gstatic)\.com/,
    new workbox.strategies.StaleWhileRevalidate()
  );

  workbox.routing.registerRoute(
    /.*googleapis\.com/,
    new workbox.strategies.StaleWhileRevalidate({
      cacheName: "googleapis",
    })
  );

  workbox.routing.registerRoute(
    /.*gstatic\.com/,
    new workbox.strategies.StaleWhileRevalidate({
      cacheName: "gstatic",
    })
  );

  //Restrict Caches for a Specific Origin
  workbox.routing.registerRoute(
    "https://hacker-news.firebaseio.com/v0/api",
    new workbox.strategies.CacheFirst({
      cacheName: "stories",
      plugins: [
        new workbox.expiration.Plugin({
          maxEntries: 50,
          maxAgeSeconds: 5 * 60, // 5 minutes
        }),
        new workbox.cacheableResponse.Plugin({
          statuses: [0, 200],
        }),
      ],
    })
  );
} else {
  console.log(`Boo! Workbox didn't load 😬`);
}
