<template>
  <v-layout class="chat">
    <v-container class="chat__container pa-0 pa-md-3">
      <v-row>
        <v-col cols="12" md="10" class="chat__content-container">
          <div class="chat__scroll-down" v-show="!autoScroll" @click="scrollDown()">Scroll down</div>
          <div class="chat__content" ref="content">
            <div ref="contentWrapper">
              <chat-message v-for="(group, i) in groupedMessages" :key="i"
                            :author="group.author"
                            :posted-at="group.postedAt"

              >
                <div class="chat__group" :class="groupModifier(group)">
                  <div class="chat__single-message single-message" v-for="message in group.messages" :key="message.id">
                    <div v-html="parseContent(message.content)"></div>
                    <div class="single-message__date">{{ formatDate(message.postedAt) }}</div>
                  </div>
                </div>
              </chat-message>
            </div>
          </div>
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
import ChatMessage from '../components/ChatMessage'
import { debounce } from 'lodash'
import moment from 'moment'

export default {
  components: {UserList, ChatMessage},
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
      autoScroll: true
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
  mounted() {
    const scrollEvent =  debounce(() => {
      this.autoScroll = this.shouldScrollDown();
    }, 60);
    this.$refs.content.addEventListener('scroll', scrollEvent);
    this.$on('hook:beforeDestroy', () => {
      this.$refs.content.removeEventListener('scroll', scrollEvent);
    })
  },
  computed: {
    groupedMessages() {
      if (!this.messages.length) {
        return [];
      }

      const clonedMessages = JSON.parse(JSON.stringify(this.messages))
      const grouped = [];
      let currentGroup = {
        author: this.messages[0].author,
        postedAt: this.messages[0].postedAt,
        messages: [this.messages[0]]
      };

      for (let i = 1; i < clonedMessages.length; i++) {
        const currentMessage = clonedMessages[i];
        const timeToPreviousPost = currentMessage.postedAt - currentGroup.messages[currentGroup.messages.length - 1].postedAt;
        if (currentGroup.author !== currentMessage.author || timeToPreviousPost > 600000) {
          currentGroup.contentLength = currentGroup.messages.reduce((total, el) => total + el.content.length, 0)
          grouped.push(currentGroup)
          currentGroup = {
            author: currentMessage.author,
            postedAt: currentMessage.postedAt,
            messages: [currentMessage]
          }
        } else {
          currentGroup.messages.push(currentMessage)
        }
      }
      currentGroup.contentLength = currentGroup.messages.reduce((total, el) => total + el.content.length, 0)
      grouped.push(currentGroup);

      return grouped;
    }
  },
  methods: {
    init({chat}) {
      if (chat) {
        const scrollDown = this.autoScroll;
        this.users = chat.users
        this.messages = this.mergeMessages(chat.messages || [])
        if (scrollDown) {
          this.$nextTick(() => {
              this.scrollDown();
          });
        }
      this.firstInit = false;
      }
    },
    parseContent(content) {
      return content
          .replace(/\n/g, '<br />');
    },
    shouldScrollDown() {
      const distanceFromBottom = this.$refs.content.scrollHeight
          - this.$refs.content.getBoundingClientRect().height - this.$refs.content.scrollTop;

      return distanceFromBottom < 50;
    },
    scrollDown() {
      if (this.$refs.content) {
        this.$refs.content.scrollTop = this.$refs.content.scrollHeight;
      }
    },
    addMessage(message) {
      const shouldScrollDown = this.autoScroll;
      this.messages.push(message)
      this.$nextTick(() => {
        if (shouldScrollDown) {
          this.scrollDown();
        }
      });
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
    groupModifier(group) {
      let classes = [];
      const avgLength = group.contentLength / group.messages.length

      if (group.contentLength < 100) {
        classes = ['chat__group--normal'];
      } else if (group.contentLength < 1000) {
        classes = ['chat__group--small'];
      } else {
        classes = ['chat__group--tiny'];
      }

      if (avgLength < 50) {
        if (group.messages.length < 20 && group.messages.length >= 10) {
          classes.push('chat__group--columns-2')
        } else if (group.messages.length >= 21) {
          classes.push('chat__group--columns-3')
        }
      }

      return classes;
    }
  }
}
</script>

<style lang="scss" scoped>
.chat {

  &__content-container {
    position: relative;
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

  &__content {
    height: calc(100vh - 272px);
    overflow: auto;
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

  &__group {
    &--small {
      font-size: 0.9em;
    }

    &--tiny {
      font-size: 0.8em;
    }

    &--columns-2 {
      column-count: 2;
    }

    &--columns-3 {
      column-count: 3;
    }
  }

  .single-message {
    position: relative;
    &__date {
      position: absolute;
      top: 0;
      right: 0;
      margin-left: 20px;
      display: none;
      background-color: #fff;
      border: 1px solid #eee;
      padding: 3px;
      font-size: 12px;
    }

    &:hover {
      .single-message__date {
        display: block;
      }
    }
  }
}
</style>
