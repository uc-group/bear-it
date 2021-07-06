export default {
  name: 'ws-message',
  inject: ['socket', 'room'],
  props: {
    name: {
      type: String,
      required: true
    },
    handler: {
      type: Function,
      required: true
    }
  },
  created() {
    (async () => {
      const socket = await this.socket;
      const messageName = `${this.room}/${this.name}`;
      socket.on(messageName, this.handler);

      this.$on('hook:beforeDestroy', () => {
        socket.off(messageName, this.handler);
      })
    })()
  },
  render() {
    return null;
  }
}
