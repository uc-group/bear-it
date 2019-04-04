workbox.core.skipWaiting()
workbox.core.clientsClaim()
workbox.precaching.precacheAndRoute(self.__precacheManifest || [])

workbox.routing.registerRoute(
  '/',
  new workbox.strategies.StaleWhileRevalidate({
    cacheName: 'bear-it-runtime'
  })
)

workbox.routing.registerRoute(
  '/api/login',
  new workbox.strategies.NetworkFirst()
)

workbox.routing.registerRoute(
  /^https:\/\/fonts\.googleapis\.com/,
  new workbox.strategies.StaleWhileRevalidate({
    cacheName: 'google-fonts-stylesheets'
  })
)

workbox.routing.registerRoute(
  /^https:\/\/fonts\.gstatic\.com/,
  new workbox.strategies.CacheFirst({
    cacheName: 'google-fonts-webfonts',
    plugins: [
      new workbox.cacheableResponse.Plugin({
        statuses: [0, 200]
      }),
      new workbox.expiration.Plugin({
        maxAgeSeconds: 60 * 60 * 24 * 365
      })
    ]
  })
)

workbox.routing.registerRoute(
  /^https:\/\/avatars\d*\.githubusercontent\.com/,
  new workbox.strategies.CacheFirst({
    cacheName: 'bear-it-avatars',
    plugins: [
      new workbox.cacheableResponse.Plugin({
        statuses: [0, 200]
      }),
      new workbox.expiration.Plugin({
        maxAgeSeconds: 60 * 60 * 24 * 365
      })
    ]
  })
)
