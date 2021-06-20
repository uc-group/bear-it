<template>
    <div class="projects">
        <v-row class="align-stretch">
            <v-col class="d-flex" cols="12" md="6" xl="4" v-for="project in projects" :key="project.id" v-show="!project.removing">
                <v-card class="flex-grow-1 d-flex flex-column" :style="tileStyles(project)">
                    <v-card-title>
                        <router-link :to="{name: 'project_details', params: {id: project.id}}">{{ project.name }}</router-link>
                        <div class="flex-grow-1"></div>
                        <v-icon v-for="component in project.components">{{ component.icon }}</v-icon>
                    </v-card-title>
                    <v-card-text class="flex-grow-1">{{ project.description }}</v-card-text>
                    <v-card-actions>
                        <v-btn text :to="{name: 'project_details', params: {id: project.id}}">Details</v-btn>
                        <div class="flex-grow-1"></div>
                        <v-btn icon @click="remove(project)">
                            <v-icon>delete</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row>
            <v-btn color="primary" fab fixed bottom right :to="{name: 'project_create'}">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-row>
    </div>
</template>

<script>
    import { createNamespacedHelpers } from 'vuex'
    import storeProjectList from '~/store/modules/projectList'

    const { mapState, mapActions } = createNamespacedHelpers('projectList')

    export default {
        created() {
            this.$store.registerModule('projectList', storeProjectList)
            this.loadList()
        },
        data() {
            return {
                updating: false
            }
        },
        computed: {
            ...mapState({
                'projects': 'cachedList'
            })
        },
        methods: {
            ...mapActions(['loadList']),
            async remove(project) {
                this.updating = true
                await this.$store.dispatch('projectList/remove', project.id)
                this.updating = false
            },
            tileStyles(project) {
                return {
                    'border-left': `5px solid ${project.color}`
                }
            }
        },
        beforeDestroy() {
            this.$store.unregisterModule('projectList')
        }
    }
</script>
