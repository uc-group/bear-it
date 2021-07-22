import { client, requestHandler } from '../../../api'

export default {
  loadOlderMessages(projectId, postedAt) {
    return client.get(`/api/chat/messages?room=chat/${projectId}&before=${postedAt}`).then(requestHandler);
  },
  moveMessages(room, idList) {
    return client.post(`/api/chat/move-messages`, { room, messages: idList }).then(requestHandler);
  }
}
