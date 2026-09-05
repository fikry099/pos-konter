const CACHE_NAME = 'wa-pos-v1';

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', event => {
    // Meneruskan permintaan jaringan secara langsung untuk performa kasir real-time
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});