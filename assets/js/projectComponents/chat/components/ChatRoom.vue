<template>
  <div class="chat">
    <ws-room ref="room"
             :name="`chat/${this.room}`"
             @joined="init"
             @left="joinedRoom = false"
             @connected="connected = true"
             @disconnected="connected = false"
    >
      <ws-room-message name="message" :handler="addMessage"></ws-room-message>
      <ws-room-message name="user-list" :handler="updateUserList"></ws-room-message>
      <ws-room-message name="message-updated" :handler="updateMessage"></ws-room-message>
      <ws-room-message name="message-removed" :handler="removeMessage"></ws-room-message>
    </ws-room>
    <div class="chat__container pa-0 pa-md-3 pb-md-0">
      <div class="chat__content-container">
        <scroll-window class="chat__content col-md-10 col-12" ref="scrollWindow"
                       :scroll-to-top-boundary="300"
                       @entered-top-boundary="loadOlderMessages">
          <template v-slot:tool>
            <transition name="fade">
              <div class="chat__selection selection" v-show="selected.length > 0">
                <div class="d-flex align-center">
                  <v-select :items="['Move', 'Remove']" v-model="selectedAction" class="selection__actions" dense></v-select>
                  <span v-show="selected.length">
                    selected <strong>{{ selected.length }}</strong> {{ selected.length === 1 ? 'message' : 'messages' }}
                    (<a @click.prevent="selected = []">clear selection</a>)
                  </span>
                </div>
                <v-spacer></v-spacer>
                <v-btn color="red darken-2" class="white--text">Make it so!</v-btn>
              </div>
            </transition>
          </template>
          <template v-slot:scroll-down="{ scrollToBottom, autoScrolling, hasScroll }">
            <div class="chat__scroll-down" v-show="hasScroll && !autoScrolling" @click="scrollToBottom()">
              Scroll down
            </div>
          </template>
          <message-grouper :messages="messages">
            <template v-slot="group">
              <message-group v-bind="group"
                :selected-for-edit="editing"
                :toolbar-visible="connected"
                :selected.sync="selected"
                @edit="editMessage"
                @delete="deleteMessage"></message-group>
            </template>
          </message-grouper>
        </scroll-window>
        <div class="hidden-sm-and-down col-2 d-flex flex-column">
          <user-list :users="users"></user-list>
          <div class="flex-grow-1"></div>
          <v-btn @click="emojiDialog = true" color="secondary" x-small>Emoji 😍 (Ctrl + M)</v-btn>
          <emoji-dialog :visible.sync="emojiDialog" @selected="emoji => message += emoji">
          </emoji-dialog>
        </div>
      </div>
      <div class="chat__form">
          <template v-if="!connected || !joinedRoom" class="chat__connecting">
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
            Connecting...
          </template>
          <textarea v-else class="chat__textarea" :class="{'chat__textarea--edit-mode': !!editing}"
                    v-model="message" @keypress.enter.prevent="sendMessage"
                    placeholder="Enter your message here..." ref="messageArea"></textarea>
      </div>
    </div>
    <confirm-dialog :message="removeConfirmMessage"
                    @cancel="confirmRemoveDialog = false"
                    @confirm="confirmMessageRemoval"
                    :show-dialog.sync="confirmRemoveDialog"
    ></confirm-dialog>
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
import EmojiDialog from '../../../components/EmojiDialog'
import ConfirmDialog from '../../../layout/components/ConfirmDialog'

export default {
  name: 'chat-room',
  components: {ConfirmDialog, EmojiDialog, MessageGroup, UserList, ScrollWindow, MessageGrouper, WsRoom, WsRoomMessage},
  props: {
    room: String
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
      emojiDialog: false,
      editing: null,
      removing: null,
      confirmRemoveDialog: false,
      selected: [],
      selectedAction: 'Move'
    }
  },
  created() {
    const openEmoji = (event) => {
      if (event.code === 'KeyM' && event.ctrlKey) {
        this.emojiDialog = true
      }
    }

    window.addEventListener('keypress', openEmoji, true);
    this.$on('hook:beforeDestroy', () => {
      window.removeEventListener('keypress', openEmoji, true);
    })
  },
  mounted() {
    this.currentOldestMessageDate = Date.now();
    this.loadOlderMessages(this.room);
  },
  computed: {
    removeConfirmMessage() {
      if (!this.removing) {
        return '';
      }

      const message = this.messages.find((m) => m.id === this.removing);
      if (!message) {
        return '';
      }

      return `Are you sure you want to remove message "${message.content.substr(0, 200)}${message.content.length > 200 ? '...' : ''}"?`;
    }
  },
  methods: {
    async loadOlderMessages() {
      if (!this.currentOldestMessageDate) {
        return
      }

      const height = this.$refs.scrollWindow.getContentHeight()
      const olderMessages = await api.loadOlderMessages(this.room, this.currentOldestMessageDate)
      this.hasOlderMessages = olderMessages.length < 100
      this.messages = this.mergeMessages(olderMessages)
      if (this.messages.length) {
        this.currentOldestMessageDate = this.messages[0].postedAt
      }
      this.$nextTick(() => {
        this.$refs.scrollWindow.scrollToDiff(this.$refs.scrollWindow.getContentHeight() - height)
      })
    },
    init({chat, users}) {
      this.joinedRoom = true
      if (chat) {
        this.users = users
        this.messages = this.mergeMessages(chat.messages || [])
        if (this.firstInit) {
          this.$nextTick(async () => {
            if (this.$refs.scrollWindow) {
              await this.$refs.scrollWindow.scrollToBottom()
              if (this.messages.length < 100) {
                this.hasOlderMessages = false
              }

              this.ready = true
            }

            this.$refs.messageArea.focus();
          })
        }
        if (this.messages.length) {
          this.currentOldestMessageDate = this.messages[0].postedAt
        }
        this.firstInit = false
      }
    },
    addMessage(message) {
      this.messages.push({...message, deleted: false })
    },
    formatDate(timestamp) {
      return moment(timestamp).format('HH:mm:ss YYYY-MM-DD')
    },
    sendMessage(event) {
      if (event.ctrlKey) {
        this.message += '\n'
        return
      }

      const content = this.message.replace(/^\s+|\s+$/, '')
      if (!content) {
        this.message = ''
        return
      }

      const room = this.$refs.room;
      if (this.editing) {
        const id = this.editing;
        room.sendMessage('edit-message', {roomId: this.room, content, id})
        this.editing = null;
      } else {
        room.sendMessage('message', {roomId: this.room, content})
      }
      this.message = ''
    },
    confirmMessageRemoval() {
      if (!this.removing || !this.connected) {
        return;
      }

      const message = this.messages.find((m) => m.id === this.removing)
      if (message) {
        message.deleted = true;
      }

      this.$refs.room.sendMessage('remove-message', this.removing);
      this.confirmRemoveDialog = false;
    },
    updateUserList(users) {
      this.users = users
    },
    mergeMessages(messages) {
      const newMessages = messages.filter(message => !this.messages.find((m) => m.id === message.id));
      const mergedMessages = [...this.messages, ...newMessages.map((message) => ({ ...message, deleted: false }))]
      mergedMessages.sort((a, b) => a.postedAt < b.postedAt ? -1 : 1)

      return mergedMessages
    },
    editMessage(id) {
      const message = this.messages.find((m) => m.id === id)
      if (!message) {
        return;
      }

      this.message = message.content;
      this.editing = id;
      this.$nextTick(() => {
        this.$refs.messageArea.focus();
      });
    },
    deleteMessage(id) {
      this.removing = id;
      this.confirmRemoveDialog = true;
    },
    updateMessage(message) {
      const index = this.messages.findIndex((m) => m.id === message.id)
      if (index !== -1) {
        this.messages.splice(index, 1, message);
      }
    },
    removeMessage(id) {
      const index = this.messages.findIndex((m) => m.id === id)
      if (index !== -1) {
        this.messages.splice(index, 1);
      }
    }
  },
  watch: {
    emojiDialog(to) {
      if (!to && this.$refs.messageArea) {
        this.$nextTick(() => {
          this.$refs.messageArea.focus();
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.chat {
  flex-grow: 1;
  position: relative;

  &__container {
    display: flex;
    flex-direction: column;
  }

  &__content-container {
    display: flex;
    flex-grow: 1;
  }

  &__content {
    height: calc(100vh - 270px);
  }

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

    &--edit-mode {
      background-color: #eae191;
    }
  }

  &__selection {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background-color: #b2dbfb;
    z-index: 1;
    padding: 10px;
    border: 1px solid #1976d2;
    border-radius: 5px;
    display: flex;
    align-items: center;
  }
}

.selection {
  &__actions {
    max-width: 100px;
    margin-right: 20px;
    padding-top: 10px;
  }
}
</style>
