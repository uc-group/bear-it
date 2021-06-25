<template>
  <v-layout class="chat">
    <v-container class="chat__container pa-0 pa-md-3">
      <v-row>
        <v-col cols="12" md="10" class="chat__content-container">
          <scroll-window class="chat__content" ref="scrollWindow">
            <template v-slot:scroll-down="{ scrollToBottom, autoScrolling, hasScroll }">
              <div class="chat__scroll-down" v-show="hasScroll && !autoScrolling && ready" @click="scrollToBottom()">Scroll down</div>
            </template>
            <message-grouper :messages="messages">
                <template v-slot="group">
                  <message-group v-bind="group"></message-group>
                </template>
            </message-grouper>
          </scroll-window>
        </v-col>
        <v-col cols="12" md="2" class="hidden-sm-and-down">
          <user-list :users="users"></user-list>
        </v-col>
      </v-row>
      <v-row class="chat__form">
        <v-col cols="12" class="chat__form-col pa-4 pa-md-0">
          <template v-if="!connected" class="chat__connecting">
            <v-progress-circular indeterminate color="primary"></v-progress-circular> Connecting...
          </template>
          <textarea v-else class="chat__textarea" v-model="message" @keyup.enter="sendMessage" placeholder="Enter your message here..."></textarea>
        </v-col>
      </v-row>
    </v-container>
  </v-layout>
</template>

<script>
import api from '../api'
import UserList from '../components/UserList'
import ScrollWindow from '~/layout/components/ScrollWindow'
import MessageGrouper from '../components/MessageGrouper'
import moment from 'moment'
import MessageGroup from '../components/MessageGroup'

export default {
  components: {MessageGroup, UserList, ScrollWindow, MessageGrouper},
  props: {
    project: Object
  },
  data() {
    return {
      connected: false,
      message: '',
      messages: [],
      users: [],
      firstInit: true,
      ready: false,
    }
  },
  created() {
    (async () => {
      const socket = await api.connection();
      const onConnect = () => {
          api.joinProject(this.project.id, this.init)
          this.connected = true
      }
      const onDisconnect = () => {
        this.connected = false
      }

      if (socket.connected) {
        api.joinProject(this.project.id, this.init)
        this.connected = true;
      }

      socket.on('connection-ready', onConnect);
      socket.on('disconnect', onDisconnect);
      socket.on(`chat/${this.project.id}/message`, this.addMessage);
      socket.on(`chat/${this.project.id}/user-list`, this.updateUserList);

      this.$on('hook:beforeDestroy', () => {
        socket.off('connection-ready', onConnect);
        socket.off('disconnect', onDisconnect);
        socket.off(`chat/${this.project.id}/message`, this.addMessage);
        socket.off(`chat/${this.project.id}/user-list`, this.updateUserList);
        api.leaveProject(this.project.id);
      })

      this.socket = socket;
    })();
  },
  methods: {
    init({chat}) {
      if (chat) {
        this.users = chat.users
        this.messages = this.mergeMessages(chat.messages || [])
        if (this.firstInit) {
          this.$nextTick(async () => {
            if (this.$refs.scrollWindow) {
              await this.$refs.scrollWindow.scrollToBottom();
              this.ready = true;
            }
          });
        }
        this.firstInit = false;
      }
    },
    addMessage(message) {
      this.messages.push(message)
    },
    formatDate(timestamp) {
      return moment(timestamp).format('HH:mm:ss YYYY-MM-DD');
    },
    sendMessage(event) {
      if (event.ctrlKey) {
        this.message += '\n';
        return;
      }

      if (!this.socket) {
        return;
      }

      const content = this.message.replace(/^\s+|\s+$/, '');
      if (!content) {
        this.message = '';
        return;
      }

      this.socket.emit(`chat/${this.project.id}/message`, { roomId: this.project.id, content })
      this.message = '';
    },
    updateUserList(users) {
      this.users = users
    },
    mergeMessages(messages) {
      const mergedMessages = [...this.messages, ...messages.filter(message => !this.messages.find((m) => m.id === message.id))];
      mergedMessages.sort((a, b) => a.postedAt < b.postedAt ? -1 : 1);

      return mergedMessages;
    },
  }
}
</script>

<style lang="scss" scoped>
.chat {
  &__scroll-down {
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translate(-50%, -50%);
    font-weight: 600;
    background-color: #eee;
    padding: 5px 15px;
    border: #ddd 1px solid;
    border-radius: 5px;
    z-index: 1000;
    cursor: pointer;
  }

  &__content {
    height: calc(100vh - 272px);
  }

  &__form {
    height: 86px;
  }

  &__form-col {
    padding: 12px 0 0 0;
  }

  &__textarea {
    width: 100%;
    height: 100%;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 15px;
  }
}
</style>
