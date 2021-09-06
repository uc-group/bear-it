import store from '~/store'
import chatRouting from '@plugins/chat/routing';
import chatConfig from '@plugins/chat/config';
import graphConfig from '@plugins/graph/config';
import pagesConfig from '@plugins/pages/config';
import testRouting from '@plugins/test-component/routing';
import testConfig from '@plugins/test-component/config';

const components = {};
const routes = [];

const registerModule = (moduleName, { config, routes: moduleRoutes }) => {
  if (moduleRoutes) {
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
          const project = to.params.project || store.state.project
          if (!project || !(project.components || []).includes(to.meta.moduleName)) {
            store.dispatch('stopFetching')
            next({name: 'not_found'})
            return;
          }
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
      console.log(route)
      routes.push(route)
    })
  }

  if (config) {
    components[moduleName] = {
      id: moduleName,
      ...config
    }
  }
}

registerModule('chat', {config: chatConfig, routes: chatRouting});
registerModule('graph', {config: graphConfig});
registerModule('pages', {config: pagesConfig});
registerModule('test-component', {config: testConfig, routes: testRouting});

export const getProjectComponents = () => components;

export const getRoutes = () => routes;
