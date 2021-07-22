<template>
  <chat-message :author="author" :posted-at="postedAt">
    <div class="message-group" :class="modifierClasses">
      <div class="chat__single-message single-message"
           :class="{
              'single-message--selected-edit': selectedForEdit === message.id,
              'single-message--deleted': message.deleted
            }"
           v-for="message in messages" :key="message.id">
        <div class="single-message__date" v-show="selectionVisible">{{ formatTimestamp(message.postedAt, 'HH:mm') }}</div>
        <div class="single-message__container" :class="{
          'single-message__container--with-checkbox': selectionVisible,
          'single-message__container--touching': touching
        }"
            @touchstart="touchstarted(message.id)"
            @touchmove="clearTouchTimer"
            @touchend="clearTouchTimer"
        >
          <transition name="fade">
          <v-checkbox class="ma-0 pa-0 single-message__checkbox" dense :value="message.id" v-model="_selected" v-show="selectionVisible"></v-checkbox>
          </transition>
          <div @click="onClicked(message.id)" class="single-message__content" v-html="parseContent(message.content)"></div>
        </div>
        <div class="single-message__toolbar" v-if="message.author === $store.state.user.id"
             v-show="toolbarVisible && !message.deleted">
          <a href="#" @click.prevent="$emit('edit', message.id)">EDIT</a>
          <a href="#" @click.prevent="$emit('delete', message.id)">DELETE</a>
        </div>
      </div>
    </div>
  </chat-message>
</template>

<script>
import ChatMessage from './ChatMessage'
import { formatTimestamp } from '~/lib/DateUtils'
import { debounce } from 'lodash'

export default {
  components: {ChatMessage},
  props: {
    author: String,
    postedAt: Number,
    modifiers: Array,
    messages: Array,
    selectedForEdit: String,
    toolbarVisible: Boolean,
    selected: Array
  },
  data() {
    return {
      ctrlPressed: false,
      touching: false
    }
  },
  mounted() {
    const keyDown = debounce((e) => {
      if (e.keyCode === 17) {
        this.ctrlPressed = true
      }
    }, 100, {
      'leading': true,
      'trailing': false
    })
    const keyUp = (e) => {
      if (e.keyCode === 17) {
        this.ctrlPressed = false
      }
    }
    window.addEventListener('keydown', keyDown);
    window.addEventListener('keyup', keyUp);
    this.$on('hook:beforeDestroy', () => {
      window.removeEventListener('keydown', keyDown);
      window.removeEventListener('keyup', keyUp);
    })
  },
  computed: {
    modifierClasses() {
      return this.modifiers.map((m) => `message-group--${m}`)
    },
    _selected: {
      get() {
        return this.selected || []
      },
      set(value) {
        this.$emit('update:selected', value)
      }
    },
    selectionVisible() {
      return this.ctrlPressed || this.selected.length > 0
    }
  },
  methods: {
    formatTimestamp,
    parseContent(content) {
      return content.replace(/\n/g, '<br />');
    },
    touchstarted(id) {
      this.clearTouchTimer();
      this.touching = true;
      this.touchtimer = setTimeout(() => {
        if (!this._selected.includes(id)) {
          this._selected.push(id)
        }
      }, 1000);
    },
    clearTouchTimer() {
      this.touching = false;
      if (this.touchtimer) {
        clearTimeout(this.touchtimer);
      }
    },
    onClicked(id) {
      if (this.selectionVisible) {
        const index = this._selected.findIndex((i) => i === id);
        if (index !== -1) {
          this._selected.splice(index, 1);
        } else {
          this._selected.push(id);
        }
      }
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
    $sm: &;
    position: relative;

    &--selected-edit {
      background-color: #eae191;
    }

    &--deleted {
      color: #999;
    }

    &__date {
      position: absolute;
      top: 0;
      left: 0;
      transform: translate(-100%, 0);
      margin-left: -5px;
      padding: 3px;
      font-size: 10px;
      color: #ccc;
    }

    &__container {
      display: flex;
      align-items: flex-start;
      will-change: padding-left;
      transition: padding-left .1s ease-in;
      padding-left: 0;

      &--with-checkbox {
        padding-left: 32px;

        .single-message__content {
          cursor: pointer;
        }
      }

      &--touching {
        user-select: none;
      }
    }

    &__checkbox {
      position: absolute;
      top: 0;
      left: 0;
    }

    &__toolbar {
      position: absolute;
      top: 0;
      right: 0;
      display: none;
      padding: 3px;
      font-size: 10px;
      color: #ccc;
      background-color: #fff;
      border: 1px solid #aaa;
    }

    &:hover {
      .single-message__date {
        display: block!important;
      }

      .single-message__toolbar {
        display: block;
      }
    }
  }
}
</style>
