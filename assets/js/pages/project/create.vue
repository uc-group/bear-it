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
                                label="ID"
                                ref="idField"
                                :error-messages="idErrors"
                                :counter="36"
                                required
                                @input="fieldChanged('id')"
                                @blur="$v.project.id.$touch()"
                        ></v-text-field>
                        <v-text-field
                                v-model="project.shortId"
                                label="Short ID"
                                :error-messages="shortIdErrors"
                                :counter="12"
                                required
                                @input="fieldChanged('shortId')"
                                @blur="$v.project.shortId.$touch()"
                        ></v-text-field>
                        <v-text-field
                                v-model="project.name"
                                label="Name"
                                ref="nameField"
                                :error-messages="nameErrors"
                                :counter="80"
                                required
                                @input="fieldChanged('name')"
                                @blur="$v.project.name.$touch()"
                        ></v-text-field>
                        <v-textarea
                                v-model="project.description"
                                label="Description"
                        ></v-textarea>
                        <div>
                            <v-label>Color</v-label>
                            <v-color-picker
                                    :swatches="swatches"
                                    mode="hexa"
                                    show-swatches
                                    v-model="project.color"
                                    @input="$v.project.color.$touch()"
                            ></v-color-picker>
                        </div>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="primary" @click="createProject" :loading="submitting">Create new project</v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </v-layout>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import { required, maxLength, minLength, helpers } from 'vuelidate/lib/validators'
    import api from '@api/project'
    import offlineMixin from '~/mixins/offline'

    const defaultSwatches = [
        ['#50FFB1', '#3C896D', '#4D685A'],
        ['#42F2F7', '#46ACC2', '#498C8A'],
        ['#EE6055', '#ED9390', '#FF9B85'],
        ['#483519', '#946E45', '#D4AA7D'],
        ['#F5EDB1', '#FFF07C', '#E9CE2C']
    ]

    const idValidator = helpers.regex('idValidator', /^[A-Z][A-Z0-9]*$/)
    const colorValidator = helpers.regex('colorValidator', /^#([0-9a-f]{3}|[0-9a-f]{6})$/i)

    export default {
        mixins: [validationMixin, offlineMixin],
        validations: {
            project: {
                id: { required, maxLength: maxLength(36), minLength: minLength(3), idValidator },
                shortId: { required, maxLength: maxLength(12), minLength: minLength(3), idValidator },
                name: { required, maxLength: maxLength(80), minLength: minLength(3) },
                color: { maxLength: maxLength(7), colorValidator }
            }
        },
        data() {
            return {
                project: {
                    id: '',
                    shortId: '',
                    name: '',
                    description: '',
                    color: this.randomSwatch()
                },
                swatches: defaultSwatches,
                serverErrors: {},
                submitting: false
            }
        },
        mounted() {
            this.$refs.idField.focus()
        },
        methods: {
            async createProject() {
                this.$v.$touch()
                if (!this.$v.$invalid) {
                    try {
                        this.submitting = true
                        await api.create(
                            this.project.id,
                            this.project.shortId,
                            this.project.name,
                            this.project.description,
                            this.project.color
                        ).then(() => {
                            this.$store.dispatch('alerts/addMessage', {
                                text: `Project "${this.project.name}" successfully created`,
                                type: 'success'
                            })
                            this.$router.push({ name: 'project_details', params: { id: this.project.id } })
                        })
                    } catch (error) {
                        if (error.offline) {
                            this.putOfflineEvent('project_create', {
                                id: this.project.id,
                                name: this.project.name,
                                description: this.project.description,
                                color: this.project.color
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
            randomSwatch() {
                let swatchGroup = defaultSwatches[Math.floor(Math.random() * defaultSwatches.length)]

                return swatchGroup[Math.floor(Math.random() * swatchGroup.length)]
            },
            fieldChanged(field) {
                this.$v.project[field].$touch()
                if (this.serverErrors.hasOwnProperty(field)) {
                    this.serverErrors[field] = null
                }
            }
        },
        watch: {
            'project.id': function (newValue) {
                this.project.id = newValue.toUpperCase()
            },
            'project.shortId': function (newValue) {
                this.project.shortId = newValue.toUpperCase()
            }
        },
        computed: {
            idErrors() {
                const errors = []
                if (!this.$v.project.id.$dirty) return errors
                !this.$v.project.id.maxLength && errors.push('Id must be at most 36 characters long')
                !this.$v.project.id.minLength && errors.push('Id must be at least 3 characters long')
                !this.$v.project.id.required && errors.push('Id is required')
                !this.$v.project.id.idValidator && errors.push('Id can only contain uppercase alphanumeric values')

                if (this.serverErrors.hasOwnProperty('id') && this.serverErrors.id) {
                    errors.push(this.serverErrors.id)
                }

                return errors
            },
            shortIdErrors() {
                const errors = []
                if (!this.$v.project.shortId.$dirty) return errors
                !this.$v.project.shortId.maxLength && errors.push('Short ID must be at most 12 characters long')
                !this.$v.project.shortId.minLength && errors.push('Short ID must be at least 3 characters long')
                !this.$v.project.shortId.required && errors.push('Short ID is required')
                !this.$v.project.shortId.idValidator && errors.push('Short ID can only contain uppercase alphanumeric values')

                if (this.serverErrors.hasOwnProperty('shortId') && this.serverErrors.shortId) {
                    errors.push(this.serverErrors.shortId)
                }

                return errors
            },
            nameErrors() {
                const errors = []
                if (!this.$v.project.name.$dirty) return errors
                !this.$v.project.name.maxLength && errors.push('Name must be at most 80 characters long')
                !this.$v.project.name.minLength && errors.push('Name must be at least 3 characters long')
                !this.$v.project.name.required && errors.push('Name is required')

                if (this.serverErrors.hasOwnProperty('name') && this.serverErrors.name) {
                    errors.push(this.serverErrors.name)
                }

                return errors
            },
            colorErrors() {
                const errors = []
                if (!this.$v.project.color.$dirty) return errors
                !this.$v.project.color.colorValidator && errors.push('Color must be in hex format #FFF or #FFFFFF')

                return errors
            }
        }
    }
</script>
