import { client, requestHandler } from '../../../api'
import { io } from 'socket.io-client'

let socket = null


async function connect() {
  const token = await client.post('/api/auth-token').then(requestHandler)
  socket = io(`ws://localhost:3000`, {
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
  leaveProject: (projectId) => {
    socket.emit('leave-room', { roomId: `chat/${projectId}` });
  }
}
