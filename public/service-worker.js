// Service Worker untuk PWA - Offline Support & Caching
const CACHE_NAME = 'shop85-v1';
const ASSETS_TO_CACHE = [
  '/css/adminlte.min.css',
  '/js/jquery.min.js',
  '/js/bootstrap.bundle.min.js',
  '/js/adminlte.min.js',
  '/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
  '/images/icon-192.png',
];

// Install Service Worker & Cache Assets
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[Service Worker] Caching assets');
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.log('[Service Worker] Some assets failed to cache:', err);
      });
    })
  );
  self.skipWaiting(); // Force new SW to activate immediately
});

// Activate Service Worker & Clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((cacheName) => cacheName !== CACHE_NAME)
          .map((cacheName) => {
            console.log('[Service Worker] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          })
      );
    })
  );
  self.clients.claim(); // Claim all clients immediately
});

// Fetch Event - Cache First, Then Network (for assets), Network First (for API)
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // API requests: Network first, fallback to cache
  if (url.pathname.includes('/api/') || url.pathname.includes('/livewire/')) {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Cache successful responses
          if (response.ok) {
            const cache = caches.open(CACHE_NAME);
            cache.then((c) => c.put(request, response.clone()));
          }
          return response;
        })
        .catch(() => {
          // Return cached response if network fails
          return caches.match(request).then((cached) => cached || offlineResponse());
        })
    );
    return;
  }

  // Assets: Cache first, then network
  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) {
        return cached;
      }
      return fetch(request)
        .then((response) => {
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }
          const cache = caches.open(CACHE_NAME);
          cache.then((c) => c.put(request, response.clone()));
          return response;
        })
        .catch(() => offlineResponse());
    })
  );
});

// Offline fallback response
function offlineResponse() {
  return new Response(
    '<h1>Offline Mode</h1><p>Koneksi internet tidak tersedia. Beberapa fitur mungkin tidak berfungsi.</p>',
    {
      headers: { 'Content-Type': 'text/html' },
      status: 503,
      statusText: 'Service Unavailable',
    }
  );
}

// Background Sync (optional - untuk sinkronisasi data saat online kembali)
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-data') {
    event.waitUntil(
      fetch('/api/sync')
        .then((response) => {
          console.log('[Service Worker] Data synced:', response);
        })
        .catch((error) => {
          console.log('[Service Worker] Sync failed:', error);
        })
    );
  }
});

// Push Notification Handler (optional)
self.addEventListener('push', (event) => {
  if (!event.data) return;

  const data = event.data.json();
  const options = {
    body: data.body || 'Notifikasi baru',
    icon: '/images/icon-192.png',
    badge: '/images/icon-72.png',
    tag: data.tag || 'default',
    requireInteraction: data.requireInteraction || false,
  };

  event.waitUntil(self.registration.showNotification(data.title || 'Toko Manager', options));
});

// Notification Click Handler
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Jika window sudah terbuka, fokus ke window tersebut
      for (const client of clientList) {
        if (client.url === '/' && 'focus' in client) {
          return client.focus();
        }
      }
      // Jika tidak ada window terbuka, buka window baru
      if (clients.openWindow) {
        return clients.openWindow(event.notification.data?.url || '/');
      }
    })
  );
});
