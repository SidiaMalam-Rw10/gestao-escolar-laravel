/**
 * Service Worker — No Skola PWA
 * Precache básico do login + cache de estáticos. NUNCA cacheia páginas
 * autenticadas (evita vazamento de dados entre utilizações).
 */

const VERSION = 'noskola-v1';
const STATIC_CACHE = VERSION + '-static';
const SHELL_CACHE = VERSION + '-shell';
const PRECACHE = [
    '/login',
    '/manifest.webmanifest',
    '/logo.png',
    '/Image.jpeg',
    '/pwa/icon-192.png',
    '/pwa/icon-512.png',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(SHELL_CACHE)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
            .catch(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((k) => k.startsWith('noskola-') && k !== STATIC_CACHE && k !== SHELL_CACHE)
                        .map((k) => caches.delete(k))
                )
            )
            .then(() => self.clients.claim())
    );
});

function sameOrigin(url) {
    return new URL(url).origin === self.location.origin;
}

function isStatic(url) {
    if (!sameOrigin(url)) return false;
    return /^\/(storage|pwa|build|assets|favicon\.ico|logo\.png|Image\.jpeg)/.test(url.pathname);
}

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response && response.ok) {
            const clone = response.clone();
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, clone);
        }
        return response;
    } catch (err) {
        return cached;
    }
}

async function staleWhileRevalidate(request) {
    const cached = await caches.match(request);
    const network = fetch(request)
        .then((response) => {
            if (response && (response.ok || response.type === 'opaque')) {
                const clone = response.clone();
                caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone));
            }
            return response;
        })
        .catch(() => cached);

    return cached || network;
}

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // Nunca cachear o próprio service worker
    if (url.pathname.startsWith('/sw.js')) return;

    // Navegação: rede primeiro, offline serve /login em cache
    if (request.mode === 'navigate' && sameOrigin(url)) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) return response;
                    return caches.match('/login');
                })
                .catch(() => caches.match('/login'))
        );
        return;
    }

    if (isStatic(url)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // CDN (fontawesome, alpinejs, chart.js): stale-while-revalidate
    event.respondWith(staleWhileRevalidate(request));
});