<template>
  <v-layout>
    <v-container>
      <h2>Dashboard</h2>
      {{ $store.state.offlineProjects.createdProjects }}
      <project-list></project-list>
    </v-container>
  </v-layout>
</template>

<script>
  import ProjectList from './project/list'
  export default {
    components: {
      ProjectList
    },
    props: {
      projects: Array
    }
  }
</script>
