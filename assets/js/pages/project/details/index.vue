<template>
    <v-layout>
        <v-container>
            <v-row>
                <v-col>
                    <h2>{{ currentProject.name }}</h2>
                </v-col>
            </v-row>
            <router-view :project="currentProject"></router-view>
        </v-container>
    </v-layout>
</template>

<script>
    import projectStore from '~/store/modules/project'

    export default {
        props: {
            project: Object
        },
        created() {
            this.$store.registerModule('project', projectStore(this.project))
        },
        beforeDestroy() {
            this.$store.unregisterModule('project')
        },
      computed: {
          currentProject() {
            return this.$store.state.project
          }
      }
    }
</script>
