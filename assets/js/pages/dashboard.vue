<template>
  <v-layout>
    <v-container>
      <offline-projects></offline-projects>
      <h2>Projects</h2>
      <project-list></project-list>
    </v-container>
  </v-layout>
</template>

<script>
  import ProjectList from './project/list'
  import OfflineProjects from './dashboard/components/OfflineProjects'

  export default {
    components: {
      ProjectList,
      OfflineProjects
    },
    props: {
      projects: Array
    }
  }
</script>
