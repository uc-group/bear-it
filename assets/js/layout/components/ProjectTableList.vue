<template>
    <v-data-table :headers="headers" :items="projects" sort-by="id">
        <template v-slot:top>
            <confirm-dialog :show-dialog="showRemovalDialog" message="Are you sure you want to delete this project?"
                            @confirm="confirmProjectRemoval" @cancel="closeRemovalDialog"></confirm-dialog>
        </template>
        <template v-slot:item.name="{ item }">
            <router-link :to="{ name: 'project_details', params: {id: item.id} }">{{ item.name }}</router-link>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-btn icon :href="`/project/${item.id}/export`">
              <v-icon>mdi-archive-arrow-down</v-icon>
            </v-btn>
            <v-btn icon @click="removeProject(item)">
                <v-icon>delete</v-icon>
            </v-btn>
        </template>
        <template v-slot:no-data>
            <span>You don't have any projects yet.</span>
        </template>
    </v-data-table>
</template>

<script>
import { mapState } from 'vuex'
import ConfirmDialog from "./ConfirmDialog";

export default {
    name: "ProjectTableList",
    components: {ConfirmDialog},
    data() {
        return {
            showRemovalDialog: false,
            projectToRemove: null
        }
    },
    computed: {
        ...mapState({
            projects: state => state.projectList.cachedList
        }),
        headers() {
            return [
                { text: 'ID', value: 'id' },
                { text: 'Name', value: 'name' },
                { text: 'Actions', value: 'actions', sortable: false },
            ]
        }
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
        }
    }
}
</script>
