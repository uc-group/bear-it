<template>
  <div>
    <v-btn color="primary" @click="dialogVisible = true">Create new book</v-btn>
    <v-dialog v-model="dialogVisible" persistent max-width="600px">
      <v-card>
        <v-card-title>New book</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="createBook" :disabled="submitting">
            <v-text-field
                v-model="name"
                label="Name"
                ref="nameField"
                :error-messages="nameErrors"
                :counter="80"
                required
                @blur="$v.name.$touch()"
                :autofocus="dialogVisible"
            ></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="dialogVisible = false">Cancel</v-btn>
          <v-btn color="primary" @click="createBook" :loading="submitting">Create New Book</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
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
    projectId: String
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
    async createBook() {
      this.$v.$touch();
      if (!this.$v.$invalid && !this.submitting) {
        this.submitting = true;
        try {
          const id = await api.createBook(this.projectId, this.name).then((data) => data.id);
          this.$router.go({
            name: 'book',
            book: id
          });
          this.name = '';
        } catch (error) {
        } finally {
          this.submitting = false;
        }
      }
    }
  }
}
</script>
