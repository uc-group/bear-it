<template>
  <div class="mermaid">
    <div class="mermaid__error" v-show="error">{{ error }}</div>
    <div ref="graph"></div>
  </div>
</template>

<script>
import mermaidAPI from 'mermaid';

export default {
  props: {
    value: String
  },
  data() {
    return {
      error: '',
    }
  },
  computed: {
    id() {
      return `graph-${this._uid}`;
    }
  },
  mounted() {
    this.render();

    const resizeObserver = new ResizeObserver((entries) => {
      this.$nextTick(() => {
        this.render();
      });
    });
    resizeObserver.observe(this.$el);
    this.$on('hook:beforeDestroy', () => {
      resizeObserver.disconnect();
    });
  },
  methods: {
    render() {
      if (!this.$el) {
        return;
      }

      this.$nextTick(() => {
        const value = JSON.parse(JSON.stringify(this.value));
        if (!value.replace(/^\s+|\s+$/, '').length) {
          this.$refs.graph.innerHTML = '';
          return;
        }

        try {
          this.error = '';
          if (mermaidAPI.parse(value)) {
            mermaidAPI.render(this.id, value, (svgCode) => {
              this.$refs.graph.innerHTML = svgCode;
            });
          }
        } catch (e) {
          if (e.str) {
            this.error = e.str;
          } else {
            this.error = e.toString();
          }
        }
      })
    }
  },
  watch: {
    value() {
      this.render();
    }
  }
}
</script>

<style lang="scss" scoped>
.mermaid {
  position: relative;

  &__error {
    position: absolute;
    inset: 0 0 0 0;
    color: #d55;
    background-color: rgba(0, 0, 0, 0.8);
    padding: 20px;
    font-weight: 600;
    font-size: 1.2em;
    min-height: 100px;
  }
}
</style>
