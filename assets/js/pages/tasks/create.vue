<template>
    <v-layout>
        <v-container>
            <v-card>
                <v-card-title>Create new task</v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="createTask">
                        <v-text-field
                            v-model="task.title"
                            label="Title"
                            :error-messages="titleErrors"
                            :counter="80"
                            ref="titleField"
                            required
                            @input="fieldChanged('title')"
                            @blur="$v.task.title.$touch()"
                        ></v-text-field>
                        <v-textarea
                            v-model="task.description"
                            label="Description"
                        ></v-textarea>
                        <v-text-field
                            v-model="task.projectId"
                            label="Project ID"
                            :counter="36"
                            required
                            @input="fieldChanged('projectId')"
                            @blur="$v.task.projectId.$touch()"
                        ></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="primary" @click="createTask" :loading="submitting">Create new task</v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-layout>
</template>

<script>
import { validationMixin } from 'vuelidate'
import { maxLength, minLength, required } from "vuelidate/lib/validators"
import offlineMixin from '~/mixins/offline'
import api from '~/api/task'

export default {
    mixins: [validationMixin, offlineMixin],
    validations: {
        task: {
            title: { required, maxLength: maxLength(80), minLength: minLength(3) },
            projectId: { required, maxLength: maxLength(36), minLength: minLength(3) }
        }
    },
    data() {
        return {
            task: {
                title: '',
                description: '',
                projectId: ''
            },
            serverErrors: {},
            submitting: false
        }
    },
    mounted() {
        this.$refs.titleField.focus()
    },
    watch: {
        'task.projectId': function (newValue) {
            this.task.projectId = newValue.toUpperCase()
        }
    },
    computed: {
        titleErrors() {
            const errors = []
            if (!this.$v.task.title.$dirty) return errors
            !this.$v.task.title.maxLength && errors.push('Title must be at most 80 characters long')
            !this.$v.task.title.minLength && errors.push('Title must be at least 3 characters long')
            !this.$v.task.title.required && errors.push('Title is required')

            if (this.serverErrors.hasOwnProperty('title') && this.serverErrors.title) {
                errors.push(this.serverErrors.title)
            }

            return errors
        },
        projectIdErrors() {
            const errors = []
            if (!this.$v.task.projectId.$dirty) return errors
            !this.$v.task.projectId.maxLength && errors.push('Project ID must be at most 36 characters long')
            !this.$v.task.projectId.minLength && errors.push('Project ID must be at least 3 characters long')
            !this.$v.task.projectId.required && errors.push('Project ID is required')

            if (this.serverErrors.hasOwnProperty('projectId') && this.serverErrors.projectId) {
                errors.push(this.serverErrors.projectId)
            }

            return errors
        }
    },
    methods: {
        async createTask() {
            this.$v.$touch()
            if (!this.$v.$invalid) {
                try {
                    this.submitting = true
                    api.create(this.task.title, this.task.description, this.task.projectId).then((data) => {
                        this.$store.dispatch('alerts/addMessage', {
                            text: `Task "${this.task.title}" successfully created`,
                            type: 'success'
                        })
                        this.$router.push({ name: 'task', params: { id: data.id }})
                    })
                } catch (error) {
                    if (error.offline) {
                        this.putOfflineEvent('task_create', {
                            title: this.task.title,
                            description: this.task.description
                        })
                        this.$router.push('/')
                    } else if (error.type && error.type === 'ERROR_VALIDATION') {
                        this.serverErrors = error.errorMessages
                    } else {
                        console.error(error.message)
                    }
                } finally {
                    this.submitting = false
                }
            }
        },
        fieldChanged(field) {
            this.$v.task[field].$touch()
            if (this.serverErrors.hasOwnProperty(field)) {
                this.serverErrors[field] = null
            }
        }
    }
}
</script>
