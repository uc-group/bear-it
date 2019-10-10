import api from '@api/project'
import store from '~/store'

export default [
    {
        name: 'dashboard',
        path: '/',
        component: () => import('@pages/dashboard.vue'),
        props: true
    }
]
