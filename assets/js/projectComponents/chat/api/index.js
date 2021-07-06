import { client, requestHandler } from '../../../api'

export default {
  loadOlderMessages(projectId, postedAt) {
    return client.get(`/api/chat/messages?room=chat/${projectId}&before=${postedAt}`).then(requestHandler)
  }
}
