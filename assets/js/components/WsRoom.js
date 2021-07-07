import wsSocket from '../api/ws';

export default {
  props: {
    name: {
      type: String,
      required: true
    }
  },
  provide() {
    return {
      socket: wsSocket,
      room: this.name
    }
  },
  data() {
    return {
      joinedRoom: false
    }
  },
  created() {
    (async () => {
      const socket = await wsSocket;

      const joinRoom = () => {
        socket.emit('join-room', {roomId: this.name}, (response) => {
          this.joinedRoom = true;
          this.$emit('joined', response);
        });
      }

      const onConnectionReady = () => {
        this.$emit('connected');
        joinRoom();
      };

      const onDisconnect = () => {
        this.joinedRoom = false
        this.$emit('left');
        this.$emit('disconnected')
      }

      socket.on('connection-ready', onConnectionReady);
      socket.on('disconnect', onDisconnect)

      if (socket.connected) {
        this.$emit('connected');
      } else {
        socket.connect();
      }

      joinRoom();

      this.$on('hook:beforeDestroy', () => {
        this.joinedRoom = false;
        socket.emit('leave-room', { roomId: this.name });
        socket.off('connection-ready', onConnectionReady);
        socket.off('disconnect', onDisconnect);
      })
    })();
  },
  render(h) {
    return h('div', this.$scopedSlots.default && this.$scopedSlots.default());
  },
  methods: {
    async sendMessage(name, data, callback) {
      if (!this.joinedRoom) {
        return;
      }

      (await wsSocket).emit(`${this.name}/${name}`, data, callback);
    }
  }
}
