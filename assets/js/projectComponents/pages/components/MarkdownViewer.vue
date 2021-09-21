<template>
    <div v-html="compiledMarkdown"></div>
</template>

<script>
import { debounce } from 'lodash'
import compileMarkdown from '../../../lib/compileMarkdown'

export default {
    name: "MarkdownViewer",
    props: {
        content: String
    },
    created() {
        this.compiledMarkdown = compileMarkdown(this.content)
    },
    data() {
        return {
            compiledMarkdown: null
        }
    },
    watch: {
        content: {
            immediate: true,
            handler: debounce(function (newValue) {
                this.compiledMarkdown = compileMarkdown(newValue)
            }, 200)
        }
    }
}
</script>
