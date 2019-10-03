<template>
  <v-layout>
    <v-container>
      <h2>Dashboard</h2>
      <template v-if="loading">
        <v-progress-circular
                :size="50"
                color="primary"
                indeterminate
        ></v-progress-circular>
      </template>
      <template v-else>
        <v-row class="align-stretch">
          <v-col class="d-flex" cols="12" md="6" xl="4" v-for="project in projects" :key="project.id">
            <v-card class="flex-grow-1 d-flex flex-column">
              <v-card-title>{{ project.name }}</v-card-title>
              <v-card-text class="flex-grow-1">{{ project.description }}</v-card-text>
              <v-card-actions>
                <v-btn text>Details</v-btn>
                <div class="flex-grow-1"></div>
                <v-btn icon>
                  <v-icon>delete</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-btn class="float-right" color="primary" :to="{name: 'project_create'}">Create new project</v-btn>
            </v-col>
        </v-row>
      </template>
    </v-container>
  </v-layout>
</template>

<script>
  import api from '@api/project'

  export default {
    data() {
      return {
        projects: [],
        loading: true
      }
    },
    created() {
      Promise.all([
        api.userList().then(response => {
          this.projects = response.data
        })
      ]).finally(() => {
        this.loading = false
      })
    }
  }
</script>
