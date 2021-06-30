<template>
  <div class="projects">
    <v-row>
      <v-col>
        <h2>Projects</h2>
      </v-col>
      <v-col class="text-right">
        <v-btn-toggle v-model="browsingMode" color="blue">
          <v-btn value="card">
            <v-icon>mdi-cards-variant</v-icon>
          </v-btn>
          <v-btn value="table">
            <v-icon>mdi-table</v-icon>
          </v-btn>
        </v-btn-toggle>
      </v-col>
    </v-row>
    <project-browser :mode="browsingMode" @remove="remove"></project-browser>
    <v-row>
      <v-btn color="primary" fab fixed bottom right :to="{name: 'project_create'}">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </v-row>
  </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'
import storeProjectList from '~/store/modules/projectList'
import ProjectBrowser from '../../layout/components/ProjectBrowser'

const {mapState, mapActions} = createNamespacedHelpers('projectList')

export default {
  components: {ProjectBrowser},
  created() {
    this.$store.registerModule('projectList', storeProjectList)
    this.loadList()
  },
  data() {
    return {
      updating: false,
      browsingMode: window.sessionStorage.getItem('browsingMode') || 'card'
    }
  },
  methods: {
    ...mapActions(['loadList']),
    async remove(project) {
      this.updating = true
      await this.$store.dispatch('projectList/remove', project.id)
      this.updating = false
    }
  },
  beforeDestroy() {
    this.$store.unregisterModule('projectList')
  },
  watch: {
    browsingMode(newValue) {
      window.sessionStorage.setItem('browsingMode', newValue)
    }
  }
}
</script>
