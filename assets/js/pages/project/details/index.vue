<template>
    <v-layout>
        <v-container>
            <v-row>
                <v-col>
                    <h2>{{ project.name }}</h2>
                </v-col>
            </v-row>
            <v-tabs v-model="currentTab" class="elevation-2">
                <v-tab href="#general">General</v-tab>
                <v-tab href="#members">Members</v-tab>
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
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item value="members">
                    <v-card flat tile>
                        <v-card-text>
                            <members :members.sync="members" :project-id="project.id"></members>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-container>
    </v-layout>
</template>

<script>
    import Members from './members.vue'

    export default {
        components: {
            Members
        },
        props: {
            project: Object,
            tab: String
        },
        data() {
            return {
                members: this.project.members
            }
        },
        computed: {
            currentTab: {
                get() {
                    return this.tab;
                },
                set(value) {
                    this.$router.push({
                        to: 'project_details',
                        params: {
                            id: this.project.id,
                            tab: value
                        }
                    })
                }
            }
        }
    }
</script>
