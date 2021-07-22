<template>
  <v-dialog v-model="dialogVisible" persistent max-width="600px">
    <v-card>
      <v-card-title>{{ title }}</v-card-title>
      <v-card-text>
        <v-radio-group v-model="type">
          <v-radio label="Existing channel" value="existing" />
          <v-radio label="New channel" value="new" />
        </v-radio-group>
        <v-select :items="_channels" v-model="selectedChannel" v-show="type === 'existing'"></v-select>
        <v-form @submit.prevent="createChannel" :disabled="submitting" v-show="type === 'new'">
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
        <v-btn color="primary" @click="selectChannel" :loading="submitting" v-show="type === 'existing'">Select channel</v-btn>
        <v-btn color="primary" @click="createChannel" :loading="submitting" v-show="type === 'new'">Create and select channel</v-btn>
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
    },
    channels: {
      type: Array,
      default() {
        return [];
      }
    },
    title: {
      type: String,
      default: 'Select channel'
    },
    currentChannel: String
  },
  data() {
    return {
      channel: {
        name: ''
      },
      serverErrors: {},
      submitting: false,
      selectedChannel: null,
      type: 'existing'
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
    },
    _channels() {
      if (this.currentChannel === 'general') {
        return this.channels;
      }

      return ['general', ...this.channels.filter((c) => c !== this.currentChannel)];
    },
  },
  methods: {
    async createChannel() {
      if (this.type !== 'new') {
        return;
      }
      this.$v.$touch();
      if (!this.$v.$invalid) {
        try {
          this.submitting = true
          await api.create(this.room, this.channel.name);
          this.$emit('channel:selected', this.channel.name);
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
    selectChannel() {
      if (this.type !== 'existing' || !this.channel) {
        return;
      }
      this.$emit('channel:selected', this.selectedChannel);
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
    },
    channels: {
      immediate: true,
      handler(to) {
        if ((!this.selectedChannel || !to.includes(this.selectedChannel))) {
          this.selectedChannel = to.length > 0 ? to[0] : null
        }
      }
    }
  }
}
</script>
