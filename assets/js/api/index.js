import axios from 'axios'

const instance = axios.create({})
instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

export const client = instance
