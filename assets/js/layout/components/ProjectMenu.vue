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

    <v-list-item link :to="menuItem.link"
                 v-for="menuItem in menuList" :key="`${menuItem.label}_${menuItem.link}`">
      <v-list-item-icon v-if="menuItem.icon">
        <v-icon>{{ menuItem.icon }}</v-icon>
      </v-list-item-icon>
      <v-list-item-content>
        <v-list-item-title>{{ menuItem.label }}</v-list-item-title>
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

import { mapState, mapGetters } from 'vuex'

export default {
  computed: {
    ...mapState({
      project: state => state.project,
    }),
    ...mapGetters('project', {
      menuList: 'componentMenuItems'
    }),
    inProjectContext() {
      return this.project
    }
  }
}
</script>
