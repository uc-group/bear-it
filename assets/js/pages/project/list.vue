<template>
    <div class="projects">
        <v-row class="align-stretch">
            <v-col class="d-flex" cols="12" md="6" xl="4" v-for="project in projects" :key="project.id" v-show="!project.removing">
                <v-card class="flex-grow-1 d-flex flex-column" :color="project.color">
                    <v-card-title>
                        <router-link :to="{name: 'project_details', params: {id: project.id}}">{{ project.name }}</router-link>
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
            <v-col>
                <v-btn class="float-right" color="primary" :to="{name: 'project_create'}">Create new project</v-btn>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import api from '@api/project'

    export default {
        created() {
            api.userList().then( projects => {
                projects.forEach( project => {
                    project.removing = false
                    this.projects.push(project)
                })
            })
        },
        data() {
            return {
                projects: [],
                updating: false
            }
        },
        methods: {
            async remove(project) {
                project.removing = true
                this.updating = true

                try {
                    await api.remove(project.id)
                    const index = this.projects.findIndex(p => project.id = p.id)
                    if (index >= 0) {
                        this.projects.splice(index, 1)
                    }
                } catch (e) {
                    //TODO: add snackbar
                    project.removing = false;
                }
                this.updating = false
            }
        }
    }
</script>
