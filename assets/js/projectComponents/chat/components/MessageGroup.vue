<template>
  <chat-message :author="author" :posted-at="postedAt">
    <div class="message-group" :class="modifierClasses">
      <div class="chat__single-message single-message" v-for="message in messages" :key="message.id">
        <div class="single-message__date">{{ formatTimestamp(message.postedAt, 'HH:mm:ss') }}</div>
        <div v-html="parseContent(message.content)"></div>
      </div>
    </div>
  </chat-message>
</template>

<script>
import ChatMessage from './ChatMessage'
import { formatTimestamp } from '~/lib/DateUtils'

export default {
  components: {ChatMessage},
  props: {
    author: String,
    postedAt: Number,
    modifiers: Array,
    messages: Array
  },
  computed: {
    modifierClasses() {
      return this.modifiers.map((m) => `message-group--${m}`)
    }
  },
  methods: {
    formatTimestamp,
    parseContent(content) {
      return content.replace(/\n/g, '<br />');
    }
  }
}
</script>

<style lang="scss" scoped>
.message-group {
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

  .single-message {
    position: relative;
    &__date {
      position: absolute;
      top: 0;
      left: 0;
      transform: translate(-100%, 0);
      margin-left: -5px;
      display: none;
      padding: 3px;
      font-size: 10px;
      color: #ccc;
    }

    &:hover {
      .single-message__date {
        display: block;
      }
    }
  }
}
</style>
