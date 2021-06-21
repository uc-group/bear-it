import store from '~/store'
import api from '@api/project'

const components = {};
const routes = [];

const configureRoutes = (r) => {
  r.keys().forEach((key) => {
    const moduleRoutes = r(key).default
    const moduleName = key.split('/')[1]

    moduleRoutes.forEach((route) => {
      route.path = `${moduleName}${route.path}`
      if (route.name) {
        route.name = `${moduleName}_${route.name}`
      }
      route.meta = route.meta || {}
      route.meta.moduleName = moduleName
      route.props = true

      const moduleBeforeEnter = route.beforeEnter;
      route.beforeEnter = async (to, from, next) => {
        store.dispatch('startFetching')
        try {
          if (moduleBeforeEnter) {
            await moduleBeforeEnter(to, from, next)
          }
          store.dispatch('stopFetching')
          next()
        } catch (e) {
          store.dispatch('stopFetching')
          console.error(e)
        }
      }
      routes.push(route)
    })
  })

}

const configureComponents = (r) => {
  r.keys().forEach((key) => {
    const moduleName = key.split('/')[1]
    components[moduleName] = {
      id: moduleName,
      ...r(key).default,
    }
  })
}

configureComponents(require.context('../projectComponents', true, /config.js/))
configureRoutes(require.context('../projectComponents', true, /(routing\/index.js|routing.js)/))

export const getProjectComponents = () => components;

export const getRoutes = () => routes;
