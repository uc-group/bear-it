import axios from 'axios'

const instance = axios.create({})
instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

export const client = instance

export const requestHandler = response => new Promise((resolve, reject) => {
    const body = response.data
    if (body.status === 'OK') {
        resolve(body.data)
    } else {
        reject(body.error || body.message || 'Unknown error')
    }
})
