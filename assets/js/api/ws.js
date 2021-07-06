import { client, requestHandler } from './index'
import { io } from 'socket.io-client'

let socket = null

const config = JSON.parse(document.body.dataset.jsConfig)

async function connect() {
  const token = await client.post('/api/auth-token').then(requestHandler)
  socket = io(config.wsUrl || 'ws://localhost:3000', {
    query: { token }
  })

  return new Promise((resolve) => {
    socket.once('connection-ready', () => {
      resolve(socket)
    })
  })
}

export default connect();
