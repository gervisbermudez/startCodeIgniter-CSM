// Guardar  en el cache dinamico
function actualizaCacheDinamico(dynamicCache, req, res) {
  if (res.ok) {
    return caches.open(dynamicCache).then((cache) => {
      cache.put(req, res.clone());

      return res.clone();
    });
  } else {
    return res;
  }
}

// Cache with network update
function actualizaCacheStatico(staticCache, req, APP_SHELL_INMUTABLE) {
  if (APP_SHELL_INMUTABLE.includes(req.url)) {
    // No hace falta actualizar el inmutable
    // console.log('existe en inmutable', req.url );
  } else {
    // console.log('actualizando', req.url );
    return fetch(req).then((res) => {
      return actualizaCacheDinamico(staticCache, req, res);
    });
  }
}

// Network with cache fallback / update
function manejoApiMensajes(cacheName, req) {
  if (req.clone().method === "POST") {
    // POSTEO de un nuevo mensaje
    if (self.registration.sync) {
      return req
        .clone()
        .text()
        .then((body) => {
          const bodyObj = JSON.parse(body);
          return guardarMensaje(bodyObj);
        });
    } else {
      return fetch(req);
    }
  } else {
    return fetch(req)
      .then((res) => {
        if (res.ok) {
          actualizaCacheDinamico(cacheName, req, res.clone());
          return res.clone();
        } else {
          return caches.match(req);
        }
      })
      .catch((err) => {
        return caches.match(req);
      });
  }
}
