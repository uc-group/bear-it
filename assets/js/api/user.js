import {client, requestHandler} from './'

export default {
    find(term) {
        return client.post('/api/user/find', {
            term
        }).then(requestHandler)
    }
}
