<template>
    <div class="members">
        <v-simple-table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th v-if="access.members.manage"></th>
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
                    <template v-if="access.members.changeRole(member) && !offline">
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
                    <member-actions :member="member"></member-actions>
                </td>
            </tr>
            </tbody>
        </v-simple-table>
        <v-divider></v-divider>
        <template v-if="access.members.manage">
            <v-row class="mt-12">
                <v-col>
                    <h3>Invite users</h3>
                </v-col>
            </v-row>
            <v-row class="align-baseline flex-wrap">
                <template v-if="!offline">
                <v-col class="flex-md-grow-1" cols="12" md="auto">
                    <user-search :current-users="currentUsers" :selected.sync="newUsers"
                                 :disabled="updating"></user-search>
                </v-col>
                <v-col class="d-flex justify-end flex-md-grow-0" cols="12" md="auto">
                    <v-btn color="primary" @click="addMembers" :disabled="!newUsers.length || updating">
                        Add selected users
                    </v-btn>
                </v-col>
                </template>
                <template v-else>
                    <v-col class="flex-md-grow-1" cols="12" md="auto">
                        <v-alert type="info"
                          dense
                          icon="mdi-network-off-outline"
                          prominent>Not available in offline mode.
                        </v-alert>
                    </v-col>
                </template>
            </v-row>
        </template>
    </div>
</template>

<script>
    import { createNamespacedHelpers } from 'vuex'
    import {Role} from '@lib/constants'
    import {roles as roleToLabel} from '@lib/labels'
    import UserSearch from '~/layout/components/UserSearch'
    import UserName from '~/layout/components/UserName'
    import MemberActions from '../components/MemberActions'
    import offlineMixin from '~/mixins/offline';

    const { mapState, mapGetters, mapActions } = createNamespacedHelpers('project')

    const roleChoices = (roles) => {
        const choices = []
        roles.forEach(role => {
            choices.push({text: roleToLabel[role] || role, value: role})
        })

        return choices
    }

    export default {
        mixins: [ offlineMixin ],
        filters: {
            roleLabel(value) {
                return roleToLabel[value] || value
            }
        },
        components: {
            MemberActions,
            UserSearch,
            UserName
        },
        props: {
            projectId: String
        },
        data() {
            return {
                newUsers: [],
                updating: false,
                availableRoles: roleChoices([Role.ADMIN, Role.MEMBER])
            }
        },
        computed: {
            ...mapState(['members']),
            ...mapGetters(['access']),
            currentUsers() {
                return this.members.map(member => member.username)
            }
        },
        methods: {
            ...mapActions({
                'storeAddMembers': 'addMembers',
                'storeChangeRole': 'changeRole'
            }),
            async addMembers() {
                if (!this.access.members.manage) {
                    return
                }

                this.updating = true
                await this.storeAddMembers(this.newUsers)
                this.newUsers = []
                this.updating = false
            },
            async changeRole(username, role) {
                this.updating = true
                await this.storeChangeRole({ username, role })
                this.updating = false
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
