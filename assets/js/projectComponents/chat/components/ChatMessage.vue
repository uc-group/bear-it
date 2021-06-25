<template>
    <div class="chat-message">
      <div class="chat-message__avatar">
        <v-avatar size="32" :color="user ? null : 'grey lighten-3'">
          <img v-if="user" :src="user.avatar" :alt="user.username" :title="user.username"/>
        </v-avatar>
      </div>
      <div class="chat-message__container">
        <div class="chat-message__user-bar">
          <user-name v-if="user" class="chat-message__user" :user="user" short :style="{color: projectColor}"></user-name>
          <div class="chat-message__date">{{ formatDate(postedAt) }}</div>
        </div>
        <div class="chat-message__content">
          <slot></slot>
        </div>
      </div>
    </div>
</template>

<script>
import { getUserDetails } from '~/lib/userDetails'
import UserName from '../../../layout/components/UserName'
import moment from 'moment'

export default {
  components: {UserName},
  props: {
    content: String,
    author: String,
    postedAt: Number,
    id: String
  },
  data() {
    return {
      user: null
    }
  },
  created() {
    getUserDetails(this.author).then((user) => {
      this.user = user
    });
  },
  methods: {
    formatDate(timestamp) {
      return moment(timestamp).format('HH:mm:ss YYYY-MM-DD');
    }
  },
  computed: {
    projectColor() {
      return this.$store.state.project.color;
    }
  }
}
</script>

<style lang="scss" scoped>
.chat-message {
  margin: 0;
  padding: 5px;
  display: flex;
  min-height: 65px;

  &:nth-child(even) {
    background-color: #f6f6f6;
  }

  &__container {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
  }

  &__avatar {
    margin-right: 10px;
  }

  &__user-bar {
    display: flex;
    color: #777;
  }

  &__user {
    font-weight: 600;
    font-size: 0.9em;
  }

  &__content {
    margin-top: 5px;
    word-break: break-word;
  }

  &__date {
    margin-left: 20px;
    font-size: 0.7em;
  }
}
</style>
