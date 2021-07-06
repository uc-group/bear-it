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

      socket.on('connection-ready', () => {
        this.$emit('connected');
        joinRoom();
      })

      socket.on('disconnect', () => {
        this.joinedRoom = false
        this.$emit('left');
        this.$emit('disconnected')
      })

      if (socket.connected) {
        this.$emit('connected');
      } else {
        socket.connect();
      }

      joinRoom();

      this.$on('hook:beforeDestroy', () => {
        this.joinedRoom = false;
        socket.emit('leave-room', { roomId: this.name });
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
