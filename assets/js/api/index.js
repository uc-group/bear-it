import axios from 'axios'
import store from '../store'
import offlineStorage from '../lib/offlineStorage'

const instance = axios.create({})
instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
instance.interceptors.response.use(response => {
    return response
}, response => {
    store.dispatch('checkOffline')

    return response
})

export const client = instance

export const requestHandler = response => new Promise((resolve, reject) => {
    if (response.isAxiosError) {
        if (!response.response) {
            reject({offline: true})
        } else {
            reject(response)
        }

        return;
    }

    const body = response.data
    if (body.status === 'OK') {
        resolve(body.data)
    } else {
        reject(body.error || body.message || 'Unknown error')
    }
})

export const putEventWhenOffline = event => {
    return async error => {
        if (typeof error === 'object' && error.hasOwnProperty('offline') && error.offline) {
            (await offlineStorage).put(event)
        }
    }
}
