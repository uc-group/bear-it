<template>
  <v-dialog v-model="dialogVisible" persistent max-width="600px">
    <v-card>
      <v-card-title>New channel</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="createChannel" :disabled="submitting">
          <v-text-field
              v-model="channel.name"
              label="Name"
              ref="nameField"
              :error-messages="nameErrors"
              :counter="80"
              required
              @input="fieldChanged('name')"
              @blur="$v.channel.name.$touch()"
              :autofocus="dialogVisible"
          ></v-text-field>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" text @click="dialogVisible = false">Cancel</v-btn>
        <v-btn color="primary" @click="createChannel" :loading="submitting">Create channel</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { validationMixin } from 'vuelidate'
import { required, maxLength, minLength } from 'vuelidate/lib/validators'
import api from '@api/chat'

export default {
  mixins: [validationMixin],
  validations: {
    channel: {
      name: { required, maxLength: maxLength(80), minLength: minLength(2) }
    }
  },
  props: {
    visible: Boolean,
    room: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      channel: {
        name: ''
      },
      serverErrors: {},
      submitting: false
    };
  },
  computed: {
    dialogVisible: {
      get() {
        return this.visible
      }, set(value) {
        this.$emit('update:visible', value)
      }
    },
    nameErrors() {
      const errors = [];
      if (!this.$v.channel.name.$dirty) return errors;
      !this.$v.channel.name.maxLength && errors.push('Name must be at most 80 characters long');
      !this.$v.channel.name.minLength && errors.push('Name must be at least 2 characters long');
      !this.$v.channel.name.required && errors.push('Name is required');

      if (this.serverErrors.hasOwnProperty('name') && this.serverErrors.name) {
        errors.push(this.serverErrors.name)
      }

      return errors;
    }
  },
  methods: {
    async createChannel() {
      this.$v.$touch();
      if (!this.$v.$invalid) {
        try {
          this.submitting = true
          await api.create(this.room, this.channel.name);
          this.$emit('channel:created', this.channel.name);
          this.dialogVisible = false;
        } catch (error) {
          if (error.type && error.type === 'ERROR_VALIDATION') {
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
      this.$v.channel[field].$touch();
      if (this.serverErrors.hasOwnProperty(field)) {
        this.serverErrors[field] = null
      }
    }
  },
  watch: {
    visible(to) {
      if (to) {
        this.channel = {
          name: ''
        };
        this.$v.$reset();
      }
    }
  }
}
</script>
