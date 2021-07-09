import {client, requestHandler} from './'

export default {
  create(room, name) {
    return client.post('/api/chat/channel/create', {
      name,
      room
    }).then(requestHandler)
  },
  list(room) {
    return client.get(`/api/chat/channel-list/${room}`).then(requestHandler)
  }
}
