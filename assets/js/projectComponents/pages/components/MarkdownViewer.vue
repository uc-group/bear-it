<template>
    <div v-html="compiledMarkdown"></div>
</template>

<script>
import marked from 'marked'
import DOMPurify from 'dompurify'

export default {
    name: "MarkdownViewer",
    props: {
        content: String
    },
    created() {
        this.compileMarkdown()
    },
    data() {
        return {
            compiledMarkdown: null
        }
    },
    watch: {
        content() {
            this.compileMarkdown()
        }
    },
    methods: {
        compileMarkdown() {
            if (this.content) {
                this.compiledMarkdown = DOMPurify.sanitize(marked(this.content))
            }
        }
    }
}
</script>

<style scoped>

</style>
