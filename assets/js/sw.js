importScripts('/js/lib/idb.js')
importScripts('/js/lib/db-utils.js')

workbox.core.skipWaiting()
workbox.core.clientsClaim()
workbox.precaching.precacheAndRoute(self.__precacheManifest || [])
workbox.precaching.precacheAndRoute(['/', '/js/lib/idb.js', '/js/lib/db-utils.js'])

//workbox.routing.registerNavigationRoute('/')

workbox.routing.registerRoute(
    '/favicon.ico',
    new workbox.strategies.CacheFirst()
)
var routes = ['/']

routes.forEach(function (route) {
    workbox.routing.registerRoute(
        route,
        new workbox.strategies.NetworkFirst()
    )
})

workbox.routing.registerRoute(
    '/api/login',
    ({ event }) => {
        event.respondWith(
            fetch(event.request).then(res => {
                const clonedRes = res.clone()
                clonedRes.json().then(data => {
                    bearItDb.keyval.set('loggedUser', data)
                })

                return res
            }).catch(() => bearItDb.keyval.get('loggedUser').then(user => new Response(JSON.stringify(user))))
        )
    }
)

workbox.routing.registerRoute(
    '/api/project/user-list',
    ({ event }) => {
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
    }
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
