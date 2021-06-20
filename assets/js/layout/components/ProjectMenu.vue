<template>
  <div v-if="inProjectContext">
    <v-divider></v-divider>
    <v-list-item>
      <v-list-item-content>
        <v-list-item-title class="text-h6">
          {{ project.name }}
        </v-list-item-title>
        <v-list-item-subtitle>
          {{ project.id }}
        </v-list-item-subtitle>
      </v-list-item-content>
    </v-list-item>
    <v-divider></v-divider>
    <v-list-item link :to="{ name: 'project_details', params: { id: project.id, tab: 'general' } }">
      <v-list-item-icon>
        <v-icon>mdi-information</v-icon>
      </v-list-item-icon>
      <v-list-item-content>
        <v-list-item-title>General</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
    <v-list-item link :to="{ name: 'project_settings', params: { id: project.id } }">
      <v-list-item-icon>
        <v-icon>mdi-cogs</v-icon>
      </v-list-item-icon>
      <v-list-item-content>
        <v-list-item-title>Settings</v-list-item-title>
      </v-list-item-content>
    </v-list-item>
  </div>
</template>

<script>

export default {
  computed: {
    project() {
      return this.$route.params.project
    },
    inProjectContext() {
      return this.project
    }
  }
}
</script>
