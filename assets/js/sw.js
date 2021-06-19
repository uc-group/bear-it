import { precacheAndRoute, cleanupOutdatedCaches, createHandlerBoundToURL } from 'workbox-precaching';
import { setCacheNameDetails, clientsClaim } from 'workbox-core';
import { registerRoute, NavigationRoute } from 'workbox-routing';
import { CacheFirst, NetworkOnly, StaleWhileRevalidate, NetworkFirst } from 'workbox-strategies';
import { CacheableResponsePlugin } from 'workbox-cacheable-response';
import { ExpirationPlugin } from 'workbox-expiration';

importScripts('/js/lib/idb.js')
importScripts('/js/lib/db-utils.js')

setCacheNameDetails({
  prefix: 'bit',
});

cleanupOutdatedCaches()
self.skipWaiting()
clientsClaim()
precacheAndRoute(self.__WB_MANIFEST)
precacheAndRoute([
  { url: '/', revision: '1' },
  { url: '/js/lib/idb.js', revision: '1' },
  { url: '/js/lib/db-utils.js', revision: '1' }
])

registerRoute(new NavigationRoute(createHandlerBoundToURL('/'), {
  denylist: [
    /auth-.*/,
    /sw.js/,
    /.(json|ico)$/,
    /\/(images|build|js)\//,
    /\/api.*/,
    /logout$/,
    /(_profiler|_wdt).*/
  ]
}))

registerRoute('/images/meOnlineWow.jpg', new NetworkOnly())
registerRoute('/favicon.ico', new CacheFirst())

const routes = ['/', /\/api\/project\/details/]
routes.forEach(function (route) {
    registerRoute(route, new NetworkFirst())
})

registerRoute('/api/login',({ event }) => {
    event.respondWith(
        fetch(event.request).then(res => {
            res.clone().text()
                .then(data => JSON.parse(data))
                .then(json => {
                    bearItDb.keyval.set('loggedUser', json)
                }).catch(() => {})

            return res
        }).catch(() => bearItDb.keyval.get('loggedUser').then(user => new Response(JSON.stringify(user))))
    )
})

registerRoute('/api/project/user-list', ({ event }) => {
    event.respondWith(
        fetch(event.request).then(res => {
            const clonedRes = res.clone()
            clonedRes.json().then(projectList => {
                bearItDb.updateProjectList(projectList.data)
            })

            return res
        }).catch(() => {
            bearItDb.getProjectList()
        })
    )
})

registerRoute(/^https:\/\/fonts\.googleapis\.com/, new StaleWhileRevalidate({
    cacheName: 'google-fonts-stylesheets'
}))

registerRoute(/^https:\/\/fonts\.gstatic\.com/, new CacheFirst({
        cacheName: 'google-fonts-webfonts',
        plugins: [
            new CacheableResponsePlugin({
                statuses: [0, 200]
            }),
            new ExpirationPlugin({
                maxAgeSeconds: 60 * 60 * 24 * 365
            })
        ]
    })
)

registerRoute(/^https:\/\/avatars\d*\.githubusercontent\.com/, new CacheFirst({
    cacheName: 'bear-it-avatars',
    plugins: [
        new CacheableResponsePlugin({
            statuses: [0, 200]
        }),
        new ExpirationPlugin({
            maxAgeSeconds: 60 * 60 * 24 * 365
        })
    ]
}))
