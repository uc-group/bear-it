<template>
    <v-form @submit.prevent="createPage">
        <v-text-field
            required
            label="Name"
            :counter="80"
            v-model="page.name"
            :error-messages="nameErrors"
            @input="fieldChanged('name')"
            @blur="$v.page.name.$touch()"
            ref="nameField"
        ></v-text-field>
        <markdown-editor :content="initDataContent" @input="updateFormContent"></markdown-editor>
        <v-btn depressed @click="createPage">Submit</v-btn>
    </v-form>
</template>

<script>
import MarkdownEditor from './MarkdownEditor'
import { validationMixin } from 'vuelidate'
import { maxLength, minLength, required } from 'vuelidate/lib/validators'

export default {
    name: "PageForm",
    components: { MarkdownEditor },
    mixins: [validationMixin],
    validations: {
        page: {
            name: { required, maxLength: maxLength(80), minLength: minLength(3) }
        }
    },
    props: {
        initData: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    data() {
        return {
            page: {
                name: '',
                content: ''
            }
        }
    },
    mounted() {
        this.$refs.nameField.focus()
        if (this.initData.hasOwnProperty('name')) {
            this.page.name = this.initData.name
        }

        if (this.initData.hasOwnProperty('content')) {
            this.page.content = this.initData.content
        }
    },
    computed: {
        nameErrors() {
            const errors = []
            if (!this.$v.page.name.$dirty) return errors
            !this.$v.page.name.maxLength && errors.push('Name must be at most 80 characters long')
            !this.$v.page.name.minLength && errors.push('Name must be at least 3 characters long')
            !this.$v.page.name.required && errors.push('Name is required')

            return errors
        },
        initDataContent() {
            return this.initData.hasOwnProperty('content') ? this.initData.content : ''
        }
    },
    methods: {
        updateFormContent(data) {
            this.page.content = data.content
        },
        createPage() {
            this.$v.$touch()
            if (!this.$v.$invalid) {
                this.$emit('submit', this.page)
            }
        },
        fieldChanged(fieldName) {
            this.$v.page[fieldName].$touch()
        }
    }
}
</script>

<style scoped>

</style>
