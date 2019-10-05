<template>
    <div class="members">
        <v-simple-table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="member in members">
                <td>
                    <v-avatar size="24">
                        <img :src="member.avatar" :alt="member.username" :title="member.username"/>
                    </v-avatar>
                    <user-name :user="member"></user-name>
                </td>
                <td>{{ member.role }}</td>
            </tr>
            </tbody>
        </v-simple-table>
        <v-divider></v-divider>
        <template v-if="canManageUsers">
            <v-row class="mt-12">
                <v-col>
                    <h3>Invite users</h3>
                </v-col>
            </v-row>
            <v-row>
                <v-col class="d-flex align-baseline">
                    <user-search :current-users="currentUsers" :selected.sync="newUsers" :disabled="updating"></user-search>
                    <v-btn color="primary" class="ml-5" @click="addUsers" :disabled="!newUsers.length || updating">Add selected users</v-btn>
                </v-col>
            </v-row>
        </template>
    </div>
</template>

<script>
    import UserSearch from '~/layout/components/UserSearch'
    import UserName from '~/layout/components/UserName'
    import api from '@api/project'

    export default {
        components: {
            UserSearch,
            UserName
        },
        props: {
            members: Array,
            projectId: String
        },
        data() {
            return {
                newUsers: [],
                updating: false
            }
        },
        computed: {
            currentUsers() {
                return this.members.map(member => member.username)
            },
            userRole() {
                const currentMember = this.members.find(member => member.username === this.$store.state.user.login)
                if (currentMember) {
                    return currentMember.role
                }

                return null
            },
            canManageUsers() {
                return this.userRole === 'admin' || this.userRole === 'owner'
            }
        },
        methods: {
            async addUsers() {
                if (!this.canManageUsers) {
                    return;
                }

                this.updating = true
                const currentMembers = await api.inviteUsers(this.projectId, this.newUsers)
                this.$emit('update:members', currentMembers)
                this.newUsers = []
                this.updating = false
            }
        }
    }
</script>
