import store from '~/store'
import api from '@api/project'
const components = {};
const routes = [];

const configureRoutes = (r) => {
  r.keys().forEach((key) => {
    const moduleRoutes = r(key).default
    const moduleName = key.split('/')[1]

    const enrichRoute = (route) => {
      if (route.name) {
        route.name = `${moduleName}_${route.name}`
      }
      route.meta = route.meta || {}
      route.meta.moduleName = moduleName
      route.props = true

      const moduleBeforeEnter = route.beforeEnter;
      route.beforeEnter = async (to, from, next) => {
        try {
          if (store.state.project) {
            to.params.project = store.state.project;
          } else if (typeof to.params.project === 'string') {
            to.params.project = await api.get(to.params.id)
          }
          const project = to.params.project;
          if (!project || !(project.components || []).includes(to.meta.moduleName)) {
            next({name: 'not_found'})
            return;
          }
          if (moduleBeforeEnter) {
            await moduleBeforeEnter(to, from, next)
          }
          next()
        } catch (e) {
          console.error(e)
        }
      }

      (route.children || []).forEach((childRoute) => {
        enrichRoute(childRoute);
      });
    };

    moduleRoutes.forEach((route) => {
      route.path = `${moduleName}${route.path}`
      enrichRoute(route);
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
