import { client, requestHandler } from './'

export default {
    create(title, description, projectId) {
        return client.post('/api/task/create', {
            title,
            description,
            projectId
        }).then(requestHandler)
    },
    get(id) {
        return client.get(`/api/task/details/${id}`).then(requestHandler)
    }
}