<template>
    <div class="members">
        <v-simple-table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th v-if="canManageUsers"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="member in members" v-show="!member.removing">
                <td>
                    <v-row class="align-center">
                        <v-col class="flex-grow-0">
                            <v-avatar size="24">
                                <img :src="member.avatar" :alt="member.username" :title="member.username"/>
                            </v-avatar>
                        </v-col>
                        <v-col>
                            <user-name :user="member"></user-name>
                        </v-col>
                    </v-row>
                </td>
                <td class="members__col-role">
                    <template v-if="canChangeMemberRole(member)">
                        <v-select
                                @input="value => changeRole(member.username, value)"
                                :items="availableRoles"
                                :value="member.role"
                        ></v-select>
                    </template>
                    <template v-else>
                        {{ member.role|roleLabel }}
                    </template>
                </td>
                <td class="members__col-more">
                    <v-menu>
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on">
                                <v-icon>more_vert</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item link @click.prevent>
                                <v-list-item-icon>
                                    <v-icon>mdi-face</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    Profile (@TODO)
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item @click="removeMember(member.username)" v-if="canChangeMemberRole(member)">
                                <v-list-item-icon>
                                    <v-icon>mdi-account-remove</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    Remove member
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </td>
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
            <v-row class="align-baseline flex-wrap">
                <v-col class="flex-md-grow-1" cols="12" md="auto">
                    <user-search :current-users="currentUsers" :selected.sync="newUsers"
                                 :disabled="updating"></user-search>
                </v-col>
                <v-col class="d-flex justify-end flex-md-grow-0" cols="12" md="auto">
                    <v-btn color="primary" @click="addUsers" :disabled="!newUsers.length || updating">Add selected
                        users
                    </v-btn>
                </v-col>
            </v-row>
        </template>
    </div>
</template>

<script>
    import UserSearch from '~/layout/components/UserSearch'
    import UserName from '~/layout/components/UserName'
    import api from '@api/project'
    import {Role} from '@lib/constants'
    import {roles as roleToLabel} from '@lib/labels'

    const rolesChoices = (roles) => {
        const choices = []
        roles.forEach(role => {
            choices.push({text: roleToLabel[role] || role, value: role})
        })

        return choices
    }

    export default {
        filters: {
            roleLabel(value) {
                return roleToLabel[value] || value
            }
        },
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
                updating: false,
                availableRoles: rolesChoices([Role.ADMIN, Role.MEMBER])
            }
        },
        computed: {
            currentUsers() {
                return this.members.map(member => member.username)
            },
            userUsername() {
                return this.$store.state.user.login
            },
            userRole() {
                const currentMember = this.members.find(member => member.username === this.userUsername)
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
                    return
                }

                this.updating = true
                const currentMembers = await api.inviteUsers(this.projectId, this.newUsers)
                this.$emit('update:members', currentMembers)
                this.newUsers = []
                this.updating = false
            },
            canChangeMemberRole(member) {
                return this.canManageUsers && member.role !== 'owner' && member.username !== this.userUsername
            },
            async removeMember(username) {
                this.updateMembersModel(username, member => {
                    member.removing = true
                })

                try {
                    await api.removeMember(this.projectId, username)
                    this.updateMembersModel(username, (member, index, members) => {
                        members.splice(index, 1)
                    })

                } catch (e) {
                    // TODO: display error snackbar that it failed
                    this.updateMembersModel(username, member => {
                        member.removing = false
                    })
                }
            },
            updateMembersModel(username, callback) {
                const currentMembers = JSON.parse(JSON.stringify(this.members))
                const memberIndex = currentMembers.findIndex(member => member.username === username)
                if (memberIndex < 0) {
                    return null
                }

                const result = callback(currentMembers[memberIndex], memberIndex, currentMembers)
                this.$emit('update:members', currentMembers)

                return result
            },
            async changeRole(username, newRole) {
                const oldRole = this.updateMembersModel(username, member => {
                    const oldRole = member.role
                    member.role = newRole

                    return oldRole
                })

                try {
                    await api.changeMemberRole(this.projectId, username, newRole)
                } catch (error) {
                    // TODO: display error snackbar that it failed
                    this.updateMembersModel(username, member => {
                        member.role = oldRole
                    })
                }
            }
        }
    }
</script>

<style lang="scss">
    .members {
        &__col-role {
            max-width: 150px;
            width: 150px;
        }

        &__col-more {
            text-align: center;
            max-width: 68px;
            width: 68px;
        }
    }
</style>
