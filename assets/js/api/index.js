import axios from 'axios'
import store from '../store'

const instance = axios.create({})
instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
instance.interceptors.response.use(response => {
    return response
}, response => {
    store.dispatch('checkOffline')
})

export const client = instance

export const requestHandler = response => new Promise((resolve, reject) => {
    const body = response.data
    if (body.status === 'OK') {
        resolve(body.data)
    } else {
        reject(body.error || body.message || 'Unknown error')
    }
})
