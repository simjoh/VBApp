// Service Worker for background location tracking
const CACHE_NAME = 'location-tracker-v1';
const LOCATION_CACHE = 'location-cache';

// Install event
self.addEventListener('install', (event) => {
  console.log('Service Worker installing');
  self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
  console.log('Service Worker activating');
  event.waitUntil(self.clients.claim());
});

// Background sync event
self.addEventListener('sync', (event) => {
  if (event.tag === 'background-location-sync') {
    console.log('Background sync triggered');
    event.waitUntil(syncLocationData());
  }
});

// Sync location data when online
async function syncLocationData() {
  try {
    const cache = await caches.open(LOCATION_CACHE);
    const requests = await cache.keys();

    for (const request of requests) {
      try {
        const response = await fetch(request);
        if (response.ok) {
          await cache.delete(request);
          console.log('Location data synced successfully');
        }
      } catch (error) {
        console.error('Failed to sync location data:', error);
      }
    }
  } catch (error) {
    console.error('Background sync error:', error);
  }
}

// Handle fetch events for offline caching
self.addEventListener('fetch', (event) => {
  if (event.request.url.includes('/api/location')) {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // Cache successful responses
          if (response.ok) {
            const responseClone = response.clone();
            caches.open(LOCATION_CACHE).then(cache => {
              cache.put(event.request, responseClone);
            });
          }
          return response;
        })
        .catch(() => {
          // Return cached data if offline
          return caches.match(event.request);
        })
    );
  }
});

// Handle push notifications for location updates
self.addEventListener('push', (event) => {
  if (event.data) {
    const data = event.data.json();
    const options = {
      body: data.message || 'Location update available',
      icon: '/favicon.ico',
      badge: '/favicon.ico',
      tag: 'location-update',
      requireInteraction: true
    };

    event.waitUntil(
      self.registration.showNotification(data.title || 'Location Tracker', options)
    );
  }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow('/')
  );
});
