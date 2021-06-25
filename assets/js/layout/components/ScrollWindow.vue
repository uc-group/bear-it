<template>
  <div class="scroll-window">
    <slot name="scroll-down" :scroll-to-bottom="scrollToBottom" :auto-scrolling="autoScrolling" :has-scroll="hasScroll"></slot>
    <div ref="viewport" class="scroll-window__viewport">
      <div ref="wrapper">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
import {debounce} from 'lodash'

export default {
  data() {
    return {
      autoScrolling: false,
      scrolling: false,
      hasScroll: false
    }
  },
  created() {
    this.thandler = null;
  },
  mounted() {
    const ob = new ResizeObserver((e) => {
      this.hasScroll = this.$refs.viewport.scrollHeight > this.$refs.viewport.getBoundingClientRect().height;
      if (!this.autoScrolling) {
        return;
      }

      this.scrollToBottom();
    })
    ob.observe(this.$refs.wrapper)

    const scrollEvent = debounce(() => {
      if (!this.scrolling) {
        this.autoScrolling = this.shouldScrollDown();
      }
    }, 60);

    this.$refs.viewport.addEventListener('scroll', scrollEvent);

    this.$on('hook:beforeDestroy', () => {
      ob.disconnect();
      this.$refs.viewport.removeEventListener('scroll', scrollEvent);
    });
  },
  methods: {
    shouldScrollDown() {
      const distanceFromBottom = this.$refs.viewport.scrollHeight
          - this.$refs.viewport.getBoundingClientRect().height - this.$refs.viewport.scrollTop;

      return distanceFromBottom < 50;
    },
    scrollToBottom() {
      return new Promise((resolve) => {
        if (this.thandler) {
          clearTimeout(this.thandler)
          this.thandler = null
        }

        this.scrolling = true;
        let t = Date.now();
        const maxScrollTop = this.$refs.viewport.scrollHeight - this.$refs.viewport.getBoundingClientRect().height;
        const d = (maxScrollTop - this.$refs.viewport.scrollTop);
        const smoothScroll = () => {
          if (maxScrollTop - this.$refs.viewport.scrollTop === 0) {
            this.thandler = null;
            this.scrolling = false;
            resolve();
          } else {
            const dt = (Date.now() - t) / 1000;
            this.$refs.viewport.scrollTo(0, this.$refs.viewport.scrollTop + d * dt);
            this.thandler = setTimeout(smoothScroll, 20)
          }
        }

        if (d > 3000) {
          this.$refs.viewport.scrollTop = maxScrollTop;
          this.scrolling = false;
          resolve();
        } else {
          smoothScroll();
        }
      })
    },
  }
}
</script>

<style lang="scss" scoped>
.scroll-window {
  position: relative;
  display: flex;

  &__viewport {
    overflow: auto;
    flex-grow: 1;
  }
}
</style>
