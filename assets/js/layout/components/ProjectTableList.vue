<template>
    <table>
        <thead>
            <tr>
                <th>Project name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody v-if="projects.length > 0">
            <tr v-for="project in projects">
                <td>
                    <router-link :to="{name: 'project_details', params: {id: project.id}}">{{ project.name }}</router-link>
                </td>
                <td>
                    <v-btn icon @click="emitRemove(project)">
                        <v-icon>delete</v-icon>
                    </v-btn>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td rowspan="2">You don't have any projects yet.</td></tr>
        </tbody>
    </table>
</template>

<script>
import { mapState } from 'vuex'

export default {
    name: "ProjectTableList",
    computed: {
        ...mapState({
            projects: state => state.projectList.cachedList
        })
    },
    methods: {
        emitRemove(item) {
            this.$emit('remove', item)
        },
        rowStyles(item) {
            return {
                'background-color': `${item.color}`
            }
        }
    }
}
</script>
