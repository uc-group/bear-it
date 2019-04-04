<template>
  <v-layout align-top justify-center>
    <v-flex xs12 sm8 md4>
      <v-card class="elevation-12">
        <v-toolbar dark color="primary">
          <v-toolbar-title>Login form</v-toolbar-title>
          <v-spacer></v-spacer>
        </v-toolbar>
        <v-card-actions>
          <v-layout justify-center>
            <v-btn
              color="primary"
              @click="loginWithGithub()"
              :disabled="loading !== null"
              :loading="loading === 'github'"
            >
              <fa-icon :icon="['fab', 'github']" class="btn-icon btn-icon--left"></fa-icon>
              <span>Login with github</span>
            </v-btn>
          </v-layout>
        </v-card-actions>
      </v-card>
    </v-flex>
  </v-layout>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'

@Component
export default class LoginPage extends Vue {
  loading: string | null = null

  get config() {
    return this.$store.state.config
  }

  loginWithGithub() {
    const authorizeUrl = 'https://github.com/login/oauth/authorize'
    const clientId = this.config.githubClientId

    window.location.href = `${authorizeUrl}?client_id=${clientId}`
  }
}
</script>