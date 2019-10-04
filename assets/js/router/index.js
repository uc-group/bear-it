import Vue from 'vue'
import VueRouter from 'vue-router'
import store, {loader} from '../store'
import projectRoutes from './routes/project'
import dashboardRoutes from './routes/dashboard'

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
            component: () => import('@pages/login.vue'),
            meta: {auth: false, drawer: false}
        },
        {
            path: '/create-task',
            name: 'task_create',
            component: () => import('@pages/tasks/create.vue')
        },
        ...projectRoutes
    ]
})

router.beforeEach((to, from, next) => {
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

export default router
