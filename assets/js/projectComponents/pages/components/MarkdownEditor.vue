<template>
    <v-row>
        <v-col cols="6">
            <textarea v-model="editorContent" @input="onInput" rows="20"></textarea>
        </v-col>
        <v-col cols="6">
            <div v-html="compiledMarkdown"></div>
        </v-col>
    </v-row>
</template>

<script>
import marked from 'marked'
import DOMPurify from 'dompurify'
import { debounce } from 'lodash'

export default {
    name: "MarkdownEditor",
    props: {
        content: String
    },
    mounted() {
        this.editorContent = this.content
        this.compileMarkdown()
    },
    data() {
        return {
            editorContent: null,
            compiledMarkdown: null
        }
    },
    methods: {
        compileMarkdown() {
            if (this.editorContent) {
                this.compiledMarkdown = DOMPurify.sanitize(marked(this.editorContent))
            }
        },
        onInput: debounce(function() {
            this.compileMarkdown()
            this.$emit('input', { content: this.editorContent, compiledMarkdown: this.compiledMarkdown })
        }, 500)
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
