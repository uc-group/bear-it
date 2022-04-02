<template>
    <v-dialog v-bind="{...$attrs, ...$props}" v-on="$listeners" persistent max-width="600px">
      <v-card>
        <v-card-title>New page</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="createPage" :disabled="submitting">
            <v-text-field
                v-model="name"
                label="Name"
                ref="nameField"
                :error-messages="nameErrors"
                :counter="80"
                required
                @blur="$v.name.$touch()"
                :autofocus="true"
            ></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="$emit('input', false)">Cancel</v-btn>
          <v-btn color="primary" @click="createPage" :loading="submitting">Create New Page</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<script>
import api from '../api';
import { validationMixin } from 'vuelidate'
import { required, maxLength, minLength } from 'vuelidate/lib/validators'

export default {
  mixins: [validationMixin],
  validations: {
    name: { required, maxLength: maxLength(80), minLength: minLength(2) }
  },
  props: {
    projectId: String,
    value: Boolean
  },
  data() {
    return {
      dialogVisible: false,
      submitting: false,
      name: ''
    }
  },
  computed: {
    nameErrors() {
      const errors = [];
      if (!this.$v.name.$dirty) return errors;
      !this.$v.name.maxLength && errors.push('Name must be at most 80 characters long');
      !this.$v.name.minLength && errors.push('Name must be at least 2 characters long');
      !this.$v.name.required && errors.push('Name is required');

      return errors;
    }
  },
  methods: {
    async createPage() {
      this.$v.$touch();
      if (!this.$v.$invalid && !this.submitting) {
        this.submitting = true;
        try {
          const name = this.name;
          const pageId = await api.create(this.projectId, name, `# ${this.name}\n\n`);
          this.$emit('page:created', { id: pageId, name });
        } catch (error) {
        } finally {
          this.submitting = false;
        }
      }
    }
  },
  watch: {
    value(to) {
      if (to) {
        this.$v.$reset();
        this.name = '';
      }
    }
  }
}
</script>
