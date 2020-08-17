// const CACHE_NAME = 'cache-1';
const CACHE_STATIC_NAME = "static-v-1";
const CACHE_DYNAMIC_NAME = "dynamic-v-1";
const CACHE_INMUTABLE_NAME = "inmutable";
const CACHE_DYNAMIC_LIMIT = 50;

function limpiarCache(cacheName, numeroItems) {
  caches.open(cacheName).then((cache) => {
    return cache.keys().then((keys) => {
      if (keys.length > numeroItems) {
        cache.delete(keys[0]).then(limpiarCache(cacheName, numeroItems));
      }
    });
  });
}

self.addEventListener("install", (e) => {
  const cacheProm = caches.open(CACHE_STATIC_NAME).then((cache) => {
    return cache.addAll([
      "/public/css/admin/start.min.css",
      "/public/img/admin/favicon/manifest.json",
    ]);
  });

  const cacheInmutable = caches
    .open(CACHE_INMUTABLE_NAME)
    .then((cache) =>
      cache.addAll([
        "https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css",
        "https://cdn.jsdelivr.net/npm/vue/dist/vue.js",
        "/public/js/materialize.min.js",
        "/public/js/jquery.js",
        "/public/js/jquery.nicescroll.min.js",
        "/public/fonts/roboto/Roboto-Medium.woff2",
        "/public/font/coda/Coda-Regular.woff",
        "/public/fonts/material-icons.woff2",
        "/public/fonts/roboto/Roboto-Regular.woff2",
      ])
    );

  e.waitUntil(Promise.all([cacheProm, cacheInmutable]));
});

self.addEventListener("fetch", (e) => {
  // 2- Cache with Network Fallback
  const respuesta = caches.match(e.request).then((res) => {
    console.log(res);
    if (res) return res;
    return fetch(e.request).then((newResp) => {
      caches.open(CACHE_DYNAMIC_NAME).then((cache) => {
        cache.put(e.request, newResp);
        limpiarCache(CACHE_DYNAMIC_NAME, 50);
      });
      return newResp.clone();
    });
  });

  e.respondWith(respuesta);

  // 4- Cache with network update
  // Rendimiento es crítico
  // Siempre estarán un paso atrás
  // if ( e.request.url.includes('bootstrap') ) {
  //     return e.respondWith( caches.match( e.request ) );
  // }
  // const respuesta = caches.open( CACHE_STATIC_NAME ).then( cache => {
  //     fetch( e.request ).then( newRes =>
  //             cache.put( e.request, newRes ));
  //     return cache.match( e.request );
  // });
  // e.respondWith( respuesta );
  // 3- Network with cache fallback
  // const respuesta = fetch( e.request ).then( res => {
  //     if ( !res ) return caches.match( e.request );
  //     caches.open( CACHE_DYNAMIC_NAME )
  //         .then( cache => {
  //             cache.put( e.request, res );
  //             limpiarCache( CACHE_DYNAMIC_NAME, CACHE_DYNAMIC_LIMIT );
  //         });
  //     return res.clone();
  // }).catch( err =>{
  //     return caches.match( e.request );
  // });
  // e.respondWith( respuesta );
  // 2- Cache with Network Fallback
  // const respuesta = caches.match( e.request )
  //     .then( res => {
  //         if ( res ) return res;
  //         // No existe el archivo
  //         // tengo que ir a la web
  //         console.log('No existe', e.request.url );
  //         return fetch( e.request ).then( newResp => {
  //             caches.open( CACHE_DYNAMIC_NAME )
  //                 .then( cache => {
  //                     cache.put( e.request, newResp );
  //                     limpiarCache( CACHE_DYNAMIC_NAME, 50 );
  //                 });
  //             return newResp.clone();
  //         });
  //     });
  // e.respondWith( respuesta );
  // 1- Cache Only
  // e.respondWith( caches.match( e.request ) );
});
