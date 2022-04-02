import {client, requestHandler} from './'

export default {
    resource(id) {
        return client.get(`/api/project-resource/${id}`).then(requestHandler)
    }
}