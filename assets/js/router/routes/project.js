import api from '@api/project'
import store from '~/store'

export default [
    {
        path: '/create-project',
        name: 'project_create',
        component: () => import('@pages/project/create.vue')
    },
    {
        path: '/project/:id/:tab?',
        name: 'project_details',
        component: () => import('@pages/project/details/index.vue'),
        props: true,
        async beforeEnter(to, from, next) {
            store.dispatch('startFetching')
            to.params.project = await api.get(to.params.id).then(response => response.data)
            store.dispatch('stopFetching')
            next()
        }
    }
]
