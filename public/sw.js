const CACHE = 'rochelle-pwa-v2';
const CORE = ['/offline.html', '/css/lawyer.css', '/images/lawyer-emblem.svg', '/images/lawyer-logo.png', '/images/lawyer-icon-192.png', '/images/lawyer-icon-512.png'];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(CORE)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', event => {
  event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key !== CACHE).map(key => caches.delete(key)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', event => {
  const request = event.request;
  const url = new URL(request.url);
  if (request.method !== 'GET' || url.origin !== self.location.origin || url.pathname.startsWith('/admin') || url.pathname.startsWith('/contact') || url.pathname.startsWith('/chat') || url.pathname.startsWith('/api')) return;

  if (request.mode === 'navigate') {
    event.respondWith(fetch(request).catch(() => caches.match('/offline.html')));
    return;
  }
  if (/\.(css|js|png|jpe?g|svg|webp|woff2?)$/i.test(url.pathname)) {
    event.respondWith(caches.match(request).then(cached => {
      const fresh = fetch(request).then(response => {
        if (response.ok) caches.open(CACHE).then(cache => cache.put(request, response.clone()));
        return response;
      });
      return cached || fresh;
    }));
  }
});
