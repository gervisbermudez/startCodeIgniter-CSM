const CACHE_VERSION = "20221129.964";
// eslint-disable-next-line no-undef
importScripts("https://unpkg.com/dexie@3.0.3/dist/dexie.js");

// imports
const DYNAMIC_CACHE = "dynamic-" + CACHE_VERSION;
const INMUTABLE_CACHE = "inmutable-" + CACHE_VERSION;
const APP_SHELL = ["/"];

const APP_SHELL_INMUTABLE = ["https://unpkg.com/dexie@3.0.3/dist/dexie.js"];

const CACHE_ROUTES = ["/api/"];

/**
 * Check if a url string is a imagen requets
 * @param {url} url
 * @returns boolean
 */
function checkURL(url) {
  return url.match(/\.(jpeg|jpg|gif|png)$/) !== null;
}

/**
 * Guardar  en el cache dinamico
 * @param {string} strDynamicCacheName The cache name
 * @param {*} request
 * @param {*} response
 * @returns response
 */
function actualizaCacheDinamico(strDynamicCacheName, request, response) {
  if (response.ok) {
    return caches.open(strDynamicCacheName).then((cache) => {
      cache.put(request, response.clone());
      return response.clone();
    });
  } else {
    return response;
  }
}
/**
 * This gives you the "cache only" behavior for things
 * in the cache and the "network only"
 * behavior for anything not-cached (which includes all
 * non-GET requests, as they cannot be cached).
 * @param {requets} req
 * @url https://web.dev/offline-cookbook/#cache-falling-back-to-network
 */
function cacheFallingBackNetwork(request) {
  return caches
    .match(request)
    .then(function (response) {
      return (
        response ||
        fetch(request).then((response) => {
          return actualizaCacheDinamico(DYNAMIC_CACHE, request, response);
        })
      );
    })
    .catch((error) => console.error(error));
}
// Postear mensajes a la API
function postearMensajes() {}

self.addEventListener("install", (e) => {
  e.waitUntil(
    caches.keys().then(function (names) {
      for (const name of names) {
        caches.delete(name);
      }
    })
  );

  const cacheStatic = caches
    .open(DYNAMIC_CACHE)
    .then((cache) => cache.addAll(APP_SHELL));
  const cacheInmutable = caches
    .open(INMUTABLE_CACHE)
    .then((cache) => cache.addAll(APP_SHELL_INMUTABLE));
  e.waitUntil(Promise.all([cacheStatic, cacheInmutable]));
});

self.addEventListener("activate", (e) => {
  const respuesta = caches.keys().then((keys) => {
    keys.forEach((key) => {
      if (key !== DYNAMIC_CACHE && key.includes("dynamic")) {
        return caches.delete(key);
      }
    });
  });
  e.waitUntil(respuesta);
});

function logFetch(requets, response, message) {
  const responseCloned = response.clone();
  getPostId(requets.clone()).then((requets) => {
    responseCloned.text().then(function (body) {
      try {
        console.log(`service worker log: ${message}`, {
          requets: JSON.parse(requets),
          response: body ? JSON.parse(body) : {},
        });
      } catch (error) {
        console.error(error);
      }
    });
  });
}

function handleEventCache(event) {
  // Init the cache. We use Dexie here to simplify the code. You can use any other
  // way to access IndexedDB of course.
  const db = new Dexie("post_cache");
  db.version(1).stores({
    post_cache: "key,response,timestamp",
  });
  respuesta = cacheMatch(event.request.clone(), db.post_cache).then(
    (response) => {
      if (response) {
        logFetch(event.request, response, "request found in cache: ");
        fetch(event.request.clone())
          .then((res) => {
            // If it works, put the response into IndexedDB
            const responseCloned = res.clone();
            cachePut(event.request.clone(), responseCloned, db.post_cache);
            logFetch(event.request, res, "request revalidate cache: ");
          })
          .catch(function (error) {
            console.error(error);
          });
        return response;
      } else {
        return (
          // First try to fetch the request from the server
          fetch(event.request.clone())
            .then((response) => {
              const responseCloned = response.clone();
              // If it works, put the response into IndexedDB
              cachePut(event.request.clone(), responseCloned, db.post_cache);
              logFetch(event.request, response, "request found in network: ");
              return response;
            })
            .catch(function () {
              // If it does not work, return the cached response. If the cache does not
              // contain a response for our request, it will give us a 503-response
              return new Response("", {
                status: 503,
                statusText: "Service Unavailable",
              });
            })
        );
      }
    }
  );
  return respuesta;
}

self.addEventListener("fetch", (event) => {
  if (event.request.url.indexOf("chrome-extension:") === 0) return;
  let respuesta;
  if (event.request.method === "POST") {
    respuesta = fetch(event.request);
    event.respondWith(respuesta);
    return;
  } else if (checkURL(event.request.url)) {
    respuesta = cacheFallingBackNetwork(event.request);
  } else {
    respuesta = fetch(event.request)
      .then((newRes) => {
        return actualizaCacheDinamico(DYNAMIC_CACHE, event.request, newRes);
      })
      .catch(() => {
        return caches.match(event.request).then((res) => {
          return res;
        });
      });
  }
  event.respondWith(respuesta);
});

self.addEventListener("sync", (e) => {
  if (e.tag === "nuevo-post") {
    const respuesta = postearMensajes();
    e.waitUntil(respuesta);
  }
});

self.addEventListener("message", function (event) {
  if (event.data === "skipWaiting") {
    self.skipWaiting().then(function () {
      caches.keys().then(function (names) {
        for (const name of names) {
          if (name === DYNAMIC_CACHE) {
            caches.delete(name);
          }
        }
      });
      Dexie.delete("post_cache");
    });
  }
});

/**
 * Serializes a Request into a plain JS object.
 *
 * @param request
 * @returns Promise
 */
function serializeRequest(request) {
  const serialized = {
    url: request.url,
    method: request.method,
  };

  // Only if method is not `GET` or `HEAD` is the request allowed to have body.
  if (request.method !== "GET" && request.method !== "HEAD") {
    return request
      .clone()
      .text()
      .then(function (body) {
        serialized.body = body;
        return Promise.resolve(serialized);
      });
  }
  return Promise.resolve(serialized);
}

/**
 * Serializes a Response into a plain JS object
 *
 * @param response
 * @returns Promise
 */
function serializeResponse(response) {
  const serialized = {
    headers: serializeHeaders(response.headers),
    status: response.status,
    statusText: response.statusText,
  };

  return response
    .clone()
    .text()
    .then(function (body) {
      serialized.body = body;
      return Promise.resolve(serialized);
    });
}

/**
 * Serializes headers into a plain JS object
 *
 * @param headers
 * @returns object
 */
function serializeHeaders(headers) {
  const serialized = {};
  // `for(... of ...)` is ES6 notation but current browsers supporting SW, support this
  // notation as well and this is the only way of retrieving all the headers.
  for (const entry of headers.entries()) {
    serialized[entry[0]] = entry[1];
  }
  return serialized;
}

/**
 * Creates a Response from it's serialized version
 *
 * @param data
 * @returns Promise
 */
function deserializeResponse(data) {
  return Promise.resolve(new Response(data.body, data));
}

/**
 * Saves the response for the given request eventually overriding the previous version
 *
 * @param data
 * @returns Promise
 */
function cachePut(request, response, store) {
  let key, data;
  getPostId(request.clone())
    .then(function (id) {
      key = id;
      return serializeResponse(response.clone());
    })
    .then(function (serializedResponse) {
      data = serializedResponse;
      const entry = {
        key: key,
        response: data,
        timestamp: Date.now(),
      };
      store.add(entry).catch(function () {
        store.update(entry.key, entry);
      });
    });
}

/**
 * Returns the cached response for the given request or an empty 503-response  for a cache miss.
 *
 * @param request
 * @return {Promise}
 */
function cacheMatch(request, store) {
  return getPostId(request.clone())
    .then(function (id) {
      return store.get(id);
    })
    .then(function (data) {
      if (data) {
        const responseData = deserializeResponse(data.response);
        return responseData;
      } else {
        return false;
      }
    });
}

/**
 * Returns a string identifier for our POST request.
 *
 * @param request
 * @return string
 */
function getPostId(request) {
  return serializeRequest(request.clone()).then((object) => {
    const postID = JSON.stringify(object);
    return postID;
  });
}
