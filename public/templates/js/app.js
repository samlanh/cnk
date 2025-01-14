self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('app-store').then((cache) => cache.addAll([
      '/camappgit/appweb/public/'
    ])),
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then(function (response) {
      return response || fetch(e.request);
    })
  );
});