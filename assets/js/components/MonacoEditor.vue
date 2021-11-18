<template>
  <div>
    <div ref="editorEl"></div>
  </div>
</template>

<script>
import * as monaco from 'monaco-editor';
import { initEditor as mermaid } from '../lib/editor/mermaid';

export default {
  props: {
    value: String,
    language: {
      type: String,
      default: 'text'
    }
  },
  mounted() {
    const editor = monaco.editor.create(this.$refs.editorEl, {
      value: JSON.parse(JSON.stringify(this.value)),
      language: this.language
    });
    if (this.language === 'mermaid') {
      mermaid(monaco);
    }

    editor.onDidChangeModelContent(() => {
      this.$emit('input', editor.getValue());
    })

    const resizeObserver = new ResizeObserver((entries) => {
        editor.layout({
          height: entries[0].contentRect.height,
          width: entries[0].contentRect.width
        });
    });
    resizeObserver.observe(this.$el);

    this.$on('hook:beforeDestroy', () => {
      editor.dispose();
      resizeObserver.disconnect();
    });
    this.editor = editor;
  },
  watch: {
    value(to) {
      if (this.editor && this.editor.getValue() !== to) {
        this.editor.getModel().setValue(to);
      }

    }
  }
}
</script>
