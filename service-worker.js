const CACHE_NAME = 'absensi-rfid-v2';
const urlsToCache = [
  './',
  './index.php',
  './assets/css/material-dashboard.css?v=3.0.4',
  './assets/css/nucleo-icons.css',
  './assets/css/nucleo-svg.css',
  './assets/js/core/popper.min.js',
  './assets/js/core/bootstrap.min.js',
  './assets/js/plugins/perfect-scrollbar.min.js',
  './assets/js/plugins/smooth-scrollbar.min.js',
  './assets/js/material-dashboard.min.js?v=3.0.4',
  './assets/img/logo_sekolah.png',
  './assets/img/apple-icon.png'
];

self.addEventListener('install', event => {
  self.skipWaiting(); // Force new SW to activate immediately
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  // Network-First strategy for HTML requests
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          return caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, response.clone());
            return response;
          });
        })
        .catch(() => {
          return caches.match(event.request);
        })
    );
  } else {
    // Cache-First strategy for assets
    event.respondWith(
      caches.match(event.request)
        .then(response => {
          if (response) {
            return response;
          }
          return fetch(event.request);
        })
    );
  }
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});