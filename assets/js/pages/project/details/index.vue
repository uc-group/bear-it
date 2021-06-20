<template>
    <v-layout>
        <v-container>
            <v-row>
                <v-col>
                    <h2>{{ project.name || '??' }}</h2>
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
                            <members-tab></members-tab>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-container>
    </v-layout>
</template>

<script>
    import MembersTab from './tabs/members.vue'
    import projectStore from '~/store/modules/project'

    export default {
        components: {
            MembersTab
        },
        props: {
            project: Object,
            tab: String
        },
        created() {
            this.$store.registerModule('project', projectStore(this.project))
        },
        beforeDestroy() {
            this.$store.unregisterModule('project')
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
