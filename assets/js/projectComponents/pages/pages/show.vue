<template>
  <div class="page">
    <markdown-viewer v-if="page" :content="page.content"></markdown-viewer>
  </div>
</template>

<script>
import MarkdownViewer from '../components/MarkdownViewer';

export default {
  name: 'PageShow',
  components: { MarkdownViewer },
  props: {
    page: Object
  },
  data() {
    return {}
  },
  methods: {}
}
</script>
