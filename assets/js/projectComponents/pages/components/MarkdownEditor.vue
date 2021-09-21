<template>
    <v-row>
        <v-col cols="6">
            <textarea v-model="editorContent" @input="onInput" rows="20"></textarea>
        </v-col>
        <v-col cols="6">
            <markdown-viewer :content="editorContent"></markdown-viewer>
        </v-col>
    </v-row>
</template>

<script>
import MarkdownViewer from './MarkdownViewer'

export default {
    name: "MarkdownEditor",
    components: { MarkdownViewer },
    props: {
        content: String
    },
    mounted() {
        this.editorContent = this.content
    },
    data() {
        return {
            editorContent: null,
            compiledMarkdown: null
        }
    },
    methods: {
        onInput() {
            this.$emit('input', {
                content: this.editorContent,
                compiledMarkdown: this.compiledMarkdown
            })
        }
    }
}
</script>

<style lang="scss" scoped>
textarea {
    border: none;
    border-right: 1px solid #ccc;
    resize: none;
    outline: none;
    background-color: #f6f6f6;
    font-size: 14px;
    font-family: "Monaco", courier, monospace;
    padding: 20px;
    width: 100%;
}
</style>
