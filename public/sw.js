const CACHE_NAME = 'eforvm-static-v4';
const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/assets/icons/icon-192.png',
];

// Never cache dynamic auth, payment, private files, or API endpoints
const BYPASS_PREFIXES = ['/api/', '/files/', '/login', '/register', '/logout', '/langganan', '/admin', '/profil', '/kuis'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);
    if (url.origin !== self.location.origin) return;
    if (BYPASS_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) return;

    // HTML navigation: network-first so session/CSRF/data stays fresh; offline fallback only when unreachable
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Local static assets: cache-first with background refresh
    if (url.pathname.startsWith('/assets/')) {
        event.respondWith(
            caches.match(req).then((cached) => {
                const network = fetch(req)
                    .then((res) => {
                        if (res && res.status === 200 && res.type === 'basic') {
                            const copy = res.clone();
                            caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
                        }
                        return res;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
    }
});
