<template>
  <v-layout class="settings">
    <v-container>
      <v-row>
        <v-col>
          <h2>{{ project.name || '??' }} / Settings</h2>
        </v-col>
      </v-row>
      <v-row>
        <v-card width="100%">
          <v-card-title>
            Components
            <div class="flex-grow-1"></div>
          </v-card-title>
          <v-card-subtitle>
            Manage components for this project.
          </v-card-subtitle>
          <v-card-text>
            <div class="settings__components">
              <project-component v-for="component in components"
                                 :key="component.id"
                                 :name="component.name"
                                 :description="component.description"
                                 :icon="component.icon"
                                 :active="hasComponent(component.id)"
                                 class="settings__component"
              >
                <template v-slot:actions>
                  <div class="flex-grow-1"></div>
                  <v-btn text v-if="hasComponent(component.id)" color="red"
                         @click.prevent="removeComponent(component)"
                         :disabled="changingComponents">Remove</v-btn>
                  <v-btn text v-else color="primary"
                         @click.prevent="addComponent(component)"
                         :disabled="changingComponents">Add</v-btn>
                </template>
              </project-component>
            </div>
          </v-card-text>
        </v-card>
      </v-row>
    </v-container>
  </v-layout>
</template>

<script>
import ProjectComponent from '../components/ProjectComponent'
import { getProjectComponents } from '../../../../plugins/projectComponents'
import api from '@api/project'
import { mapActions, mapState } from 'vuex'

export default {
  components: {ProjectComponent},
  props: {
    project: Object
  },
  data() {
    return {
      components: getProjectComponents(),
      changingComponents: false
    }
  },
  computed: {
    ...mapState('project', {
      projectComponents: (state) => state.components || []
    })
  },
  methods: {
    ...mapActions('alerts', {
      addAlert: 'addMessage'
    }),
    ...mapActions('project', ['updateComponents']),
    hasComponent(id) {
      return this.projectComponents.includes(id)
    },
    async addComponent(component) {
      try {
        this.updateComponents(await api.addComponent(this.project.id, component.id))
        this.addAlert({ text: `Component "${component.name}" has been added to the project`, type: 'success' })
      } catch (e) {
        this.addAlert({ text: `Adding component "${component.name}" to the project has failed :(`, type: 'error' })
      }
    },
    async removeComponent(component) {
      try {
        this.updateComponents(await api.removeComponent(this.project.id, component.id))
        this.addAlert({ text: `Component "${component.name}" has been removed to the project`, type: 'success' })
      } catch (e) {
        this.addAlert({ text: `Removing component "${component.name}" to the project has failed :(`, type: 'error' })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.settings {
  &__components {
    display: flex;
    flex-wrap: wrap;
  }

  &__component {
    margin: 15px;
  }
}
</style>
