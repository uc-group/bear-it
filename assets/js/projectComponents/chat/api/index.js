import { client, requestHandler } from '../../../api'
import { io } from 'socket.io-client'

let socket = null

const config = JSON.parse(document.body.dataset.jsConfig)

async function connect() {
  const token = await client.post('/api/auth-token').then(requestHandler)
  socket = io(config.wsUrl || 'ws://localhost:3000', {
    query: { token }
  })

  return new Promise((resolve) => {
    socket.once('connection-ready', resolve)
  })
}

export default {
  connection: async () => {
    if (!socket) {
      await connect();
    } else if (!socket.connected) {
      socket.connect();
    }

    return socket;
  },
  joinProject: (projectId, callback) => {
    socket.emit('join-room', { roomId: `chat/${projectId}` }, callback);
  },
  leaveProject: (projectId, callback) => {
    socket.emit('leave-room', { roomId: `chat/${projectId}` }, callback);
  },
  loadOlderMessages(projectId, postedAt) {
    return client.get(`/api/chat/messages?room=chat/${projectId}&before=${postedAt}`).then(requestHandler)
  }
}
