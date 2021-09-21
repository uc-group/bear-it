import api from '../api/index'

const loadPageBeforeEnter = async (to, from, next) => {
  try {
    to.params.page = await api.get(to.params.page)
    next()
  } catch (e) {
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
