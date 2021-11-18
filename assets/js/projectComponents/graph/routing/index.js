export default [
  {
    path: '/',
    name: 'index',
    component: () => import(/* webpackChunkName: "graph" */ '../pages/index.vue')
  }
]
