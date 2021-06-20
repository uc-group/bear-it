import api from '@api/project'
import store from '~/store'
import { getRoutes, getComponentConfig } from '~/plugins/projectComponents'

const loadProjectBeforeEnter = async (to, from, next) => {
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

export default [
    {
        path: '/create-project',
        name: 'project_create',
        component: () => import(/* webpackChunkName: "project" */ '@pages/project/create.vue')
    },
    {
        path: '/project/:id/settings',
        name: 'project_settings',
        component: () => import(/* webpackChunkName: "project" */ '@pages/project/settings/index.vue'),
        props: true,
        beforeEnter: loadProjectBeforeEnter
    },
    ...getRoutes(),
    {
        path: '/project/:id/:tab?',
        name: 'project_details',
        component: () => import(/* webpackChunkName: "project" */ '@pages/project/details/index.vue'),
        props: true,
        beforeEnter: loadProjectBeforeEnter
    }
]
