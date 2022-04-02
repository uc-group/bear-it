import api from '@api/task'
import store from '~/store'

export const loadTaskBeforeEnter = async (to, from, next) => {
    store.dispatch('startFetching')
    try {
        to.params.task = await api.get(`${to.params.id}-${to.params.taskNumber}`)
        store.dispatch('stopFetching')
        next()
    } catch (e) {
        store.dispatch('stopFetching')
        console.error(e)
    }
}

export default [
    {
        path: '/create-task/:projectId?',
        name: 'task_create',
        props: true,
        component: () => import('@pages/tasks/create.vue')
    }
]