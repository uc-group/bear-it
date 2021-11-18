import { client, requestHandler } from '../../../api'

export default {
    create(project, name, content) {
        return client.post('/api/page/create', {
            project,
            name,
            content
        }).then(requestHandler)
    },
    save(id, name, content) {
        return client.post('/api/page/edit', {
            id,
            name,
            content
        }).then(requestHandler)
    },
    list(project) {
        return client.get(`/api/page/${project}/list`).then(requestHandler)
    },
    get(page) {
        return client.get(`/api/page/details/${page}`).then(requestHandler)
    },
    listBooks(project) {
        return client.get(`/api/page/${project}/book-list`).then(requestHandler)
    },
    createBook(project, name) {
        return client.post(`/api/page/book`, {
            project,
            name
        }).then(requestHandler)
    },
    getBook(bookId) {
        return client.get(`/api/page/book/${bookId}`).then(requestHandler)
    }
}
