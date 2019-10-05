import {client} from './'

export default {
    create(id, name, description) {
        return client.post('/api/project/create', {
            id,
            name,
            description
        }).then(response => response.data)
    },
    userList() {
        return client.get('/api/project/user-list').then(response => response.data)
    },
    get(id) {
        return client.get(`/api/project/details/${id}`).then(response => response.data)
    }
}
