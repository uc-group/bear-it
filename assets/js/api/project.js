import {client, requestHandler} from './'

export default {
    create(id, name, description) {
        return client.post('/api/project/create', {
            id,
            name,
            description
        }).then(response => response.data)
    },
    userList() {
        return client.get('/api/project/user-list').then(requestHandler)
    },
    get(id) {
        return client.get(`/api/project/details/${id}`).then(response => response.data)
    },
    inviteUsers(id, usernames) {
        return client.post(`/api/project/members/${id}/invite`, {
            users: usernames
        }).then(requestHandler)
    },
    remove(id) {
        return new Promise( (resolve, reject) => {
            alert('Implement me')
            //return client.get('/api/project/remove').then(requestHandler)
        })
    }
}
