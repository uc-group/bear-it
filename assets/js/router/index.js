import Vue from 'vue'
import VueRouter from 'vue-router'
import store, {loader} from '../store'
import projectRoutes from './routes/project'
import dashboardRoutes from './routes/dashboard'
import taskRoutes from './routes/task'

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    scrollBehavior() {
        return {x: 0, y: 0}
    },
    routes: [
        ...dashboardRoutes,
        {
            name: 'timetracker',
            path: '/timetracker',
            component: () => import('@pages/timetracker/tracker.vue')
        },
        {
            name: 'login',
            path: '/login',
            component: () => import(/* webpackChunkName: "common" */ '@pages/login.vue'),
            meta: {auth: false, drawer: false}
        },
        ...taskRoutes,
        ...projectRoutes,
        {
            path: '*',
            name: 'not_found',
            component: {
                template: '<h1>Page not found</h1>'
            }
        }
    ]
})

router.beforeEach((to, from, next) => {
    store.dispatch('startFetching')
    loader.then(() => {
        if (
            (!to.meta.hasOwnProperty('auth') || to.meta.auth === true) &&
            !store.state.user
        ) {
            next('/login')
        } else if (to.name === 'login' && store.state.user) {
            next('/')
        }

        next()
    })
})

router.afterEach((to, from, next) => {
    store.dispatch('stopFetching')
});

export default router
