<template>
  <div class="page">
    <markdown-viewer v-if="page" :content="page.content"></markdown-viewer>
  </div>
</template>

<script>
import MarkdownViewer from '../components/MarkdownViewer';
import api from '../api/index'

export default {
  inheritAttrs: false,
  name: 'PageShow',
  components: { MarkdownViewer },
  props: {
    pageId: String
  },
  async beforeRouteEnter(to, from, next) {
    const page = await api.get(to.params.pageId);
    next((vm) => {
      vm.setPage(page)
    })
  },
  async beforeRouteUpdate(to, from, next) {
    this.setPage(await api.get(to.params.pageId));
    next();
  },
  data() {
    return {
      page: null
    }
  },
  methods: {
    setPage(page) {
      this.page = page;
    }
  }
}
</script>
