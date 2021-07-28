<template>
  <div class="page">
    <monaco-editor v-model="code" language="markdown" class="page__editor"></monaco-editor>
    <mermaid v-model="code"></mermaid>
  </div>
</template>

<script>
import MonacoEditor from '../../../components/MonacoEditor'
import Mermaid from '../../../components/Mermaid'

export default {
  components: {Mermaid, MonacoEditor},
  data() {
    return {
      code: ''
    }
  }
}
</script>

<style lang="scss" scoped>
.page {
  display: flex;
  flex-direction: column;

  &__editor {
    border: 2px solid #aaa;
    border-radius: 5px;
    height: 40vh;
  }
}
</style>
