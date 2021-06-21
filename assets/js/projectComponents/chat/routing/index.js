export default [
  {
    path: '/',
    name: 'index',
    component: () => import(/* webpackChunkName: "chat" */ '../pages/index.vue')
  }
]
