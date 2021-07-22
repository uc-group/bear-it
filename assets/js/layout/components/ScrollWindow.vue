<template>
  <div class="scroll-window">
    <slot name="tool"></slot>
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
  props: {
    scrollToTopBoundary: Number
  },
  data() {
    return {
      autoScrolling: true,
      scrolling: false,
      hasScroll: false,
      noSmooth: true,
      inTopBoundary: false,
    }
  },
  created() {
    this.thandler = null;
  },
  mounted() {
    const ob = new ResizeObserver(async (e) => {
      this.$emit('resized')
      this.hasScroll = this.getContentHeight() > this.getViewportHeight();
      if (!this.autoScrolling) {
        return;
      }

      await this.scrollToBottom();
    })
    ob.observe(this.$refs.wrapper)

    const scrollEvent = debounce(() => {
      if (!this.scrolling) {
        this.autoScrolling = this.shouldScrollDown();
      }

      const scrollY = this.$refs.viewport.scrollTop;
      if (!this.inTopBoundary && scrollY <= this.scrollToTopBoundary) {
          this.inTopBoundary = true;
          this.$emit('entered-top-boundary');
      } else if (this.inTopBoundary && scrollY > this.scrollToTopBoundary) {
        this.inTopBoundary = false;
        this.$emit('left-top-boundary');
      }

    }, 60);

    this.$refs.viewport.addEventListener('scroll', scrollEvent);

    this.$on('hook:beforeDestroy', () => {
      ob.disconnect();
      this.$refs.viewport.removeEventListener('scroll', scrollEvent);
    });
    setTimeout(() => {
      this.noSmooth = false
    }, 1000)
  },
  methods: {
    shouldScrollDown() {
      const distanceFromBottom = this.getContentHeight()
          - this.getViewportHeight() - this.$refs.viewport.scrollTop;

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
        let maxScrollTop = this.getContentHeight() - this.getViewportHeight();
        const d = (maxScrollTop - this.$refs.viewport.scrollTop);
        const smoothScroll = () => {
          maxScrollTop = this.getContentHeight() - this.getViewportHeight();
          if (maxScrollTop - this.$refs.viewport.scrollTop <= 0) {
            this.$refs.viewport.scrollTop = this.getContentHeight();
            this.thandler = null;
            this.scrolling = false;
            resolve();
          } else {
            const dt = (Date.now() - t) / 1000;
            this.$refs.viewport.scrollTo(0, this.$refs.viewport.scrollTop + d * dt);
            this.thandler = setTimeout(smoothScroll, 20)
          }
        }

        if (this.noSmooth || d > 1000) {
          this.$refs.viewport.scrollTop = maxScrollTop;
          this.scrolling = false;
          resolve();
        } else {
          smoothScroll();
        }
      })
    },
    scrollTo(top) {
      if (this.$refs.viewport) {
        this.$refs.viewport.scrollTop = top;
      }
    },
    scrollToDiff(diff) {
      this.scrollTo(this.$refs.viewport?.scrollTop + diff);
    },
    getViewportHeight() {
      return this.$refs.viewport?.getBoundingClientRect().height;
    },
    getContentHeight() {
      return this.$refs.viewport?.scrollHeight;
    }
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
