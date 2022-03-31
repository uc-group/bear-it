<template>
    <v-tabs v-model="currentTab" class="elevation-2">
        <v-tab href="#general">General</v-tab>
        <v-tab href="#members">Members</v-tab>
        <v-tab href="#tasks">Tasks</v-tab>
        <v-tab-item value="general">
            <v-card flat tile>
                <v-card-text>
                    <v-simple-table>
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ project.id }}</td>
                        </tr>
                        </tbody>
                    </v-simple-table>
                    <p>{{ project.description }}</p>
                  <v-btn :href="`/project-export/${project.id}.zip`">Export project as zip archive</v-btn>
                </v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item value="members">
            <v-card flat tile>
                <v-card-text>
                    <members-tab></members-tab>
                </v-card-text>
            </v-card>
        </v-tab-item>
        <v-tab-item value="tasks">
            <v-card flat tile>
                <v-card-text>
                    <tasks-tab></tasks-tab>
                </v-card-text>
            </v-card>
        </v-tab-item>
    </v-tabs>
</template>

<script>
import MembersTab from './tabs/members.vue'
import TasksTab from './tabs/tasks.vue'

export default {
    components: {
        MembersTab,
        TasksTab
    },
    props: {
        project: Object,
        tab: String
    },
    computed: {
        currentTab: {
            get() {
                return this.tab;
            },
            set(value) {
                let method = !this.tab ? 'replace' : 'push';

                this.$router[method]({
                    to: 'project_details',
                    params: {
                        id: this.project.id,
                        tab: value,
                        project: this.project
                    }
                })
            }
        }
    }
}
</script>
