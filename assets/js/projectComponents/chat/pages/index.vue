<template>
  <div>
    <ws-room ref="room"
             :name="`chat/${this.project.id}`"
             @joined="init"
             @left="joinedRoom = false"
             @connected="connected = true"
             @disconnected="connected = false"
    >
      <ws-room-message name="message" :handler="addMessage"></ws-room-message>
      <ws-room-message name="user-list" :handler="updateUserList"></ws-room-message>
    </ws-room>
    <v-layout class="chat">
      <v-container class="chat__container pa-0 pa-md-3">
        <v-row>
          <v-col cols="12" md="10" class="chat__content-container">
            <scroll-window class="chat__content" ref="scrollWindow"
                           :scroll-to-top-boundary="300"
                           @entered-top-boundary="loadOlderMessages">
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
            <template v-if="!connected || !joinedRoom" class="chat__connecting">
              <v-progress-circular indeterminate color="primary"></v-progress-circular> Connecting...
            </template>
            <textarea v-else class="chat__textarea" v-model="message" @keyup.enter="sendMessage" placeholder="Enter your message here..."></textarea>
          </v-col>
        </v-row>
      </v-container>
    </v-layout>
  </div>
</template>

<script>
import api from '../api'
import UserList from '../components/UserList'
import ScrollWindow from '~/layout/components/ScrollWindow'
import MessageGrouper from '../components/MessageGrouper'
import moment from 'moment'
import MessageGroup from '../components/MessageGroup'
import WsRoom from '../../../components/WsRoom'
import WsRoomMessage from '../../../components/WsRoomMessage'

export default {
  name: 'chat-page-index',
  components: {MessageGroup, UserList, ScrollWindow, MessageGrouper, WsRoom, WsRoomMessage},
  props: {
    project: Object
  },
  data() {
    return {
      connected: false,
      joinedRoom: false,
      message: '',
      messages: [],
      users: [],
      firstInit: true,
      ready: false,
      hasOlderMessages: true,
      currentOldestMessageDate: null,
    }
  },
  methods: {
    async loadOlderMessages() {
      if (!this.currentOldestMessageDate) {
        return;
      }

      const height = this.$refs.scrollWindow.getContentHeight();
      const olderMessages = await api.loadOlderMessages(this.project.id, this.currentOldestMessageDate);
      this.hasOlderMessages = olderMessages.length < 100;
      this.messages = this.mergeMessages(olderMessages);
      if (this.messages.length) {
        this.currentOldestMessageDate = this.messages[0].postedAt;
      }
      this.$nextTick(() => {
        this.$refs.scrollWindow.scrollToDiff(this.$refs.scrollWindow.getContentHeight() - height);
      })
    },
    init({chat}) {
      this.joinedRoom = true;
      if (chat) {
        this.users = chat.users
        this.messages = this.mergeMessages(chat.messages || [])
        if (this.firstInit) {
          this.$nextTick(async () => {
            if (this.$refs.scrollWindow) {
              await this.$refs.scrollWindow.scrollToBottom();
              if (this.messages.length < 100) {
                this.hasOlderMessages = false;
              }

              this.ready = true;
            }
          });
        }
        if (this.messages.length) {
          this.currentOldestMessageDate = this.messages[0].postedAt;
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

      const content = this.message.replace(/^\s+|\s+$/, '');
      if (!content) {
        this.message = '';
        return;
      }

      this.$refs.room.sendMessage('message', { roomId: this.project.id, content })
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
