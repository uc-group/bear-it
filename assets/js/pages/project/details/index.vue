<template>
    <v-layout>
        <v-container class="pa-0 pa-md-3">
            <v-row>
                <v-col>
                    <h2 class="header">
                      <span class="header__title">{{ currentProject.name }}</span>
                      <div class="header__subtitle" v-if="subtitle" v-html="subtitle"></div>
                    </h2>
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
            document.title = this.currentProject.name;
        },
        beforeDestroy() {
            document.title = 'Bear-IT';
            this.$store.unregisterModule('project')
        },
        computed: {
            subtitle() {
              return this.$store.getters.subtitle
            },
            currentProject() {
              return this.$store.state.project
            }
        }
    }
</script>

<style lang="scss" scoped>
.header {
  display: flex;
  align-items: baseline;

  &__title {
    margin-right: 10px;
  }

  &__subtitle {
    font-size: 0.8em;
  }
}
</style>
