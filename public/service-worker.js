// Service Worker untuk PWA - Offline Support & Caching
const CACHE_NAME = 'shop85-v2';
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

// Fetch Event - Network-first for admin & API, Cache-first for assets
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Network-first for admin pages to avoid stale cached 403s
  if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/transactions')) {
    event.respondWith(networkFirst(request));
    return;
  }

  // API and Livewire: Network-first
  if (url.pathname.includes('/api/') || url.pathname.includes('/livewire/')) {
    event.respondWith(networkFirst(request));
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
          return caches.open(CACHE_NAME).then((cache) => {
            cache.put(request, response.clone()).catch((err) => {
              console.log('[Service Worker] Cache put failed', err);
            });
            return response;
          });
        })
        .catch(() => offlineResponse());
    })
  );
});

// Network-first helper
async function networkFirst(request) {
  try {
    const response = await fetch(request);
    if (!response) throw new Error('No response');

    // Don't cache 403 responses
    if (response.status === 403) {
      return response;
    }

    if (response.ok) {
      try {
        const cache = await caches.open(CACHE_NAME);
        await cache.put(request, response.clone());
      } catch (e) {
        console.log('[Service Worker] Failed caching response', e);
      }
    }

    return response;
  } catch (err) {
    const cached = await caches.match(request);
    return cached || offlineResponse();
  }
}

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
