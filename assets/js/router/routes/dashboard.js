import api from '@api/project'
import store from '~/store'

export default [
    {
        name: 'dashboard',
        path: '/',
        component: () => import('@pages/dashboard.vue'),
        props: true,
        async beforeEnter(to, from, next) {
            store.dispatch('startFetching')
            to.params.projects = await api.userList().then(response => response.data)
            store.dispatch('stopFetching')
            next()
        }
    }
]
