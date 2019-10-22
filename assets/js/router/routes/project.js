import api from '@api/project'
import store from '~/store'

export default [
    {
        path: '/create-project',
        name: 'project_create',
        component: () => import(/* webpackChunkName: "project" */ '@pages/project/create.vue')
    },
    {
        path: '/project/:id/:tab?',
        name: 'project_details',
        component: () => import(/* webpackChunkName: "project" */ '@pages/project/details/index.vue'),
        props: true,
        async beforeEnter(to, from, next) {
            store.dispatch('startFetching')
            try {
                to.params.project = await api.get(to.params.id)
                store.dispatch('stopFetching')
                next()
            } catch (e) {
                store.dispatch('stopFetching')
                console.error(e)
            }
        }
    }
]
