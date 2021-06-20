import api from '@api/project'
import { getProjectComponents } from '../../plugins/projectComponents'

export default project => {
    const members = JSON.parse(JSON.stringify(project.members));
    members.forEach(member => {
        member.removing = false;
    })

    return {
        namespaced: true,
        state: {
            id: project.id,
            name: project.name,
            description: project.description,
            members,
            components: project.components
        },
        getters: {
            currentMember: (state, getters, rootState) => project.members.find(member => member.username === rootState.user.login),
            role: (state, getters) => (getters.currentMember ? getters.currentMember.role : null),
            access: (state, getters) => {
                const manageMembers = ['owner', 'admin'].includes(getters.role)
                const manageAndIsNotCurrentMember = (member =>
                    manageMembers && member.username !== getters.currentMember.username
                )

                return {
                    members: {
                        manage: manageMembers,
                        remove: manageAndIsNotCurrentMember,
                        changeRole: manageAndIsNotCurrentMember
                    }
                }
            },
            componentMenuItems: (state) => {
                const stateClone = JSON.parse(JSON.stringify(state));
                return Object.values(getProjectComponents())
                  .filter((component) => state.components.includes(component.id))
                  .map((component) => component.menu).reduce((acc, menuList) => {
                      menuList.forEach((menuItem) => {
                          let {label, link, icon} = menuItem

                          if (typeof menuItem.link === 'function') {
                            link = menuItem.link(stateClone)
                          }

                          acc.push({label, link, icon});
                      })

                      return acc
                  }, [])
            }
        },
        mutations: {
            ADD_MEMBER(state, member) {
                if (!member.hasOwnProperty('removing')) {
                    member.removing = false
                }

                state.members.push(member)
            },
            CHANGE_ROLE(state, {username, role}) {
                const member = state.members.find(m => m.username === username)
                if (!member) {
                    return;
                }

                state.members.role = role
            },
            MEMBER_REMOVING_BEGIN(state, username) {
                const member = state.members.find(m => m.username === username)
                if (!member) {
                    return;
                }

                state.members.removing = true
            },
            MEMBER_REMOVING_END(state, { username, success }) {
                const memberIndex = state.members.findIndex(m => m.username === username)
                if (memberIndex < 0) {
                    return;
                }

                if (success) {
                    state.members.splice(memberIndex, 1)
                } else {
                    state.members[memberIndex].removing = false
                }
            },
            UPDATE_COMPONENTS(state, components) {
                state.components = components
            }
        },
        actions: {
            async addMembers({commit, state}, userList) {
                const currentMembers = await api.inviteUsers(state.id, userList)
                currentMembers.forEach(member => {
                    if (state.members.findIndex(m => m.username === member.username) < 0) {
                        commit('ADD_MEMBER', member)
                    }
                })
            },
            async changeRole({commit, state}, {username, role}) {
                const member = state.members.find(m => m.username === username)
                if (!member) {
                    throw new Error(`Member ${username} not found in project`)
                }

                const oldRole = member.role
                commit('CHANGE_ROLE', {username, role})
                try {
                    await api.changeMemberRole(state.id, username, role)
                } catch (error) {
                    commit('CHANGE_ROLE', {username, oldRole})

                    throw error
                }
            },
            async removeMember({commit, state}, username) {
                commit('MEMBER_REMOVING_BEGIN', username)

                try {
                    await api.removeMember(state.id, username)
                    commit('MEMBER_REMOVING_END', {username, success: true})
                } catch (e) {
                    commit('MEMBER_REMOVING_END', {username, success: false})

                    throw e
                }
            },
            updateComponents({commit}, components) {
                commit('UPDATE_COMPONENTS', components)
            }
        }
    }
}
