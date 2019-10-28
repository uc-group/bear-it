<template>
    <div v-show="createdProjects.length">
        <h2>Not synchronized projects</h2>
        <v-simple-table>
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-left">Name</th>
                    <th colspan="2">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="project in createdProjects">
                    <td>{{ project.id }}</td>
                    <td>{{ project.name }}</td>
                    <td class="offline-projects__errors">
                        <template v-if="project.errors">
                            <p v-for="(error, field) in project.errors">
                                <strong>{{ field }}</strong> - {{ error }}
                            </p>
                        </template>
                    </td>
                    <td>
                        <v-progress-circular
                                indeterminate
                                color="primary"
                        v-show="project.sync"></v-progress-circular>
                    </td>
                </tr>
            </tbody>
        </v-simple-table>
        <hr />
    </div>
</template>

<script>
    import { createNamespacedHelpers } from 'vuex'

    const { mapState } = createNamespacedHelpers('offlineProjects')

    export default {
        computed: {
            ...mapState(['createdProjects'])
        }
    }
</script>

<style lang="scss">
    .offline-projects {
        &__errors {
            color: #d00;
            min-width: 30%;
            max-width: 50%;
        }
    }
</style>
