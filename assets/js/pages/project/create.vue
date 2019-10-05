<template>
    <v-layout>
        <v-container>
            <v-card>
                <v-card-title>Create new project</v-card-title>
                <v-card-text>Project is basic organization unit. Every action you do is in context of the project.</v-card-text>
                <v-card-text>
                    <v-form @submit.prevent="createProject">
                        <v-text-field
                                v-model="project.id"
                                label="Id"
                                ref="idField"
                                :error-messages="idErrors"
                                :counter="36"
                                required
                                @input="$v.project.id.$touch()"
                                @blur="$v.project.id.$touch()"
                        ></v-text-field>
                        <v-text-field
                                v-model="project.name"
                                label="Name"
                                ref="nameField"
                                :error-messages="nameErrors"
                                :counter="80"
                                required
                                @input="$v.project.name.$touch()"
                                @blur="$v.project.name.$touch()"
                        ></v-text-field>
                        <v-textarea
                                v-model="project.description"
                                label="Description"
                        ></v-textarea>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="primary" @click="createProject">Create new project</v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-layout>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import { required, maxLength, minLength, helpers } from 'vuelidate/lib/validators'
    import api from '@api/project'

    const idValidator = helpers.regex('idValidator', /^[A-Z][A-Z0-9]*$/)

    export default {
        mixins: [validationMixin],
        validations: {
            project: {
                id: { required, maxLength: maxLength(36), minLength: minLength(3), idValidator },
                name: { required, maxLength: maxLength(80), minLength: minLength(3) }
            }
        },
        data() {
            return {
                project: {
                    id: '',
                    name: '',
                    description: ''
                }
            }
        },
        mounted() {
            this.$refs.nameField.focus()
        },
        methods: {
            async createProject() {
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    await api.create(this.project.id, this.project.name, this.project.description)
                    this.$router.push('/')
                }
            }
        },
        watch: {
            'project.id': function (newValue, oldValue) {
                this.project.id = newValue.toUpperCase();
            }
        },
        computed: {
            idErrors() {
                const errors = []
                if (!this.$v.project.id.$dirty) return errors
                !this.$v.project.id.maxLength && errors.push('Id must be at most 80 characters long')
                !this.$v.project.id.minLength && errors.push('Id must be at least 3 characters long')
                !this.$v.project.id.required && errors.push('Id is required')
                !this.$v.project.id.idValidator && errors.push('Id can only contain uppercase alphanumeric values')

                return errors
            },
            nameErrors() {
                const errors = []
                if (!this.$v.project.name.$dirty) return errors
                !this.$v.project.name.maxLength && errors.push('Name must be at most 80 characters long')
                !this.$v.project.name.minLength && errors.push('Name must be at least 3 characters long')
                !this.$v.project.name.required && errors.push('Name is required')

                return errors
            }
        }
    }
</script>
