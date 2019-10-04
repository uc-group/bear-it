<template>
  <v-layout>
    <v-container>
      <h2>Dashboard</h2>
      <v-row class="align-stretch">
        <v-col class="d-flex" cols="12" md="6" xl="4" v-for="project in projects" :key="project.id">
          <v-card class="flex-grow-1 d-flex flex-column">
            <v-card-title>{{ project.name }}</v-card-title>
            <v-card-text class="flex-grow-1">{{ project.description }}</v-card-text>
            <v-card-actions>
              <v-btn text :to="{name: 'project_details', params: {id: project.id}}">Details</v-btn>
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
    </v-container>
  </v-layout>
</template>

<script>
  export default {
    props: {
      projects: Array
    },
    data() {
      return {
        loading: true
      }
    }
  }
</script>
