import api from '@api/task'
import store from '~/store'

const loadTaskBeforeEnter = async (to, from, next) => {
    store.dispatch('startFetching')
    try {
        to.params.task = await api.get(to.params.id)
        store.dispatch('stopFetching')
        next()
    } catch (e) {
        store.dispatch('stopFetching')
        console.error(e)
    }
}

export default [
    {
        path: '/create-task',
        name: 'task_create',
        component: () => import('@pages/tasks/create.vue')
    },
    {
        path: '/task/:id',
        name: 'task',
        component: () => import('@pages/tasks/details/index.vue'),
        beforeEnter: loadTaskBeforeEnter,
        props: true
    }
]