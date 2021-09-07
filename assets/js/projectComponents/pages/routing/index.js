import api from '../api/index'
import store from '~/store'

const loadPageBeforeEnter = async (to, from, next) => {
  store.dispatch('startFetching')
  try {
    to.params.page = await api.get(to.params.page)
    store.dispatch('stopFetching')
    next()
  } catch (e) {
    store.dispatch('stopFetching')
    console.error(e)
  }
}

export default [
  {
    path: '/',
    name: 'index',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/index.vue')
  },
  {
    path: '/create',
    name: 'create',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/create.vue')
  },
  {
    path: '/edit/:page',
    name: 'edit',
    component: () => import(/* webpackChunkName: "pages" */ '../pages/edit.vue'),
    props: true,
    beforeEnter: loadPageBeforeEnter
  }
]
