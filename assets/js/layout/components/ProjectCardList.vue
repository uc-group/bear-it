<template>
    <v-row class="align-stretch">
        <confirm-dialog :show-dialog="showRemovalDialog" message="Are you sure you want to delete this project?"
                        @confirm="confirmProjectRemoval" @cancel="closeRemovalDialog"></confirm-dialog>
        <v-col class="d-flex" cols="12" md="6" xl="4"
               v-for="project in projects" :key="project.id" v-show="!project.removing">
            <v-card class="flex-grow-1 d-flex flex-column" :style="tileStyles(project)">
                <v-card-title>
                    <router-link :to="{name: 'project_details', params: {id: project.id}}">{{ project.name }}</router-link>
                    <div class="flex-grow-1"></div>
                    <v-icon v-for="(component, index) in project.components" :key="index">{{ component.icon }}</v-icon>
                </v-card-title>
                <v-card-text class="flex-grow-1">{{ project.description }}</v-card-text>
                <v-card-actions>
                    <v-btn text :to="{name: 'project_details', params: {id: project.id}}">Details</v-btn>
                    <div class="flex-grow-1"></div>
                    <v-btn icon :href="`/project-export/${project.id}.zip`">
                      <v-icon>mdi-archive-arrow-down</v-icon>
                    </v-btn>
                    <v-btn icon @click="removeProject(project)">
                        <v-icon>delete</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col v-show="!fetching && projects.length === 0">You don't have any projects yet.</v-col>
        <v-col v-show="fetching">Loading project list...</v-col>
    </v-row>
</template>

<script>
import { mapState } from 'vuex'
import ConfirmDialog from "./ConfirmDialog";

export default {
    name: "ProjectCardList",
    components: {ConfirmDialog},
    data() {
        return {
            showRemovalDialog: false,
            projectToRemove: null
        }
    },
    computed: {
        ...mapState({
            fetching: state => state.fetching,
            projects: state => state.projectList.cachedList
        })
    },
    methods: {
        removeProject(project) {
            this.projectToRemove = project
            this.showRemovalDialog = true
        },
        confirmProjectRemoval() {
            this.$emit('remove', this.projectToRemove)
            this.closeRemovalDialog()
        },
        closeRemovalDialog() {
            this.showRemovalDialog = false
            this.projectToRemove = null
        },
        tileStyles(project) {
            return {
                'border-left': `5px solid ${project.color}`
            }
        }
    }
}
</script>
