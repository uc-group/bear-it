import api from '@api/project'
import store from '~/store'
import { loadTaskBeforeEnter } from './task'
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
        path: '/project/:id',
        name: 'project',
        component: () => import(/* webpackChunkName: "project" */ '@pages/project/details/index.vue'),
        props: true,
        beforeEnter: loadProjectBeforeEnter,
        children: [
            {
                path: 'settings',
                name: 'project_settings',
                props: true,
                component: () => import(/* webpackChunkName: "project" */ '@pages/project/details/settings/index.vue'),
            },
            {
                path: 'task/:taskNumber',
                name: 'project_task',
                props: true,
                component: () => import('@pages/tasks/details/index.vue'),
                beforeEnter: loadTaskBeforeEnter
            },
            ...getRoutes(),
            {
                path: ':tab?',
                name: 'project_details',
                component: () => import(/* webpackChunkName: "project" */ '@pages/project/details/tabs.vue'),
                props: true
            }
        ]
    }
]
