<template>
    <v-simple-table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
        </tr>
        </thead>
        <tbody>
        <tr class="clickable-row" v-for="task in tasks" :key="task.id" @click="showTask(task.id)">
            <td>{{ task.id }}</td>
            <td>{{ task.title }}</td>
        </tr>
        </tbody>
    </v-simple-table>
</template>

<script>
import { createNamespacedHelpers } from "vuex";
const { mapState } = createNamespacedHelpers('project')

export default {
    computed: {
        ...mapState(['tasks'])
    },
    methods: {
        showTask(id) {
            const [ project, number ] = id.split('-');
            this.$router.push({ name: 'project_task', params: { id: project, taskNumber: number } });
        }
    }
}
</script>