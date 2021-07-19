import {client, requestHandler} from './'
import { OfflineEvent } from '~/lib/offlineStorage'

export default {
    create(id, shortId, name, description, color) {
        return client.post('/api/project/create', {
            id,
            shortId,
            name,
            description,
            color
        }).then(requestHandler)
    },
    userList() {
        return client.get('/api/project/user-list').then(requestHandler)
    },
    get(id) {
        return client.get(`/api/project/details/${id}`).then(requestHandler)
    },
    inviteUsers(id, usernames) {
        return client.post(`/api/project/members/${id}/invite`, {
            users: usernames
        }).then(requestHandler)
    },
    changeMemberRole(id, username, newRole) {
        return client.post(`/api/project/members/${id}/change-role`, {
            member: username,
            role: newRole
        })
    },
    removeMember(id, username) {
        return client.post(`/api/project/members/${id}/remove`, {
            member: username
        })
    },
    remove(id) {
        return client.post(`/api/project/remove/${id}`).then(requestHandler)
    },
    addComponent(id, component) {
        return client.post(`/api/project/components/${id}/add`, {
            component
        }).then(requestHandler)
    },
    removeComponent(id, component) {
        return client.post(`/api/project/components/${id}/remove`, {
            component
        }).then(requestHandler)
    }
}
