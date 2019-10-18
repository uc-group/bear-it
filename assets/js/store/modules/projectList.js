import api from '@api/project'
import bearItDb from '@publiclib/db-utils'

const addUiFlags = project => {
    project.removing = false;

    return project;
}

export default {
    namespaced: true,
    state: () => ({
        serverList: [],
        cachedList: []
    }),
    mutations: {
        SET_SERVER(state, list) {
            state.serverList = list
        },
        SET_CACHED(state, list) {
            state.cachedList = list
        },
        PREPARE_REMOVING(state, id) {
            const project = state.cached.find(p => p.id === id)
            if (project) {
                project.removing = true;
            }
        },
        REMOVE(state, id) {
            const index = state.cached.findIndex(p => p.id === id)
            if (index >= 0) {
                state.cached.splice(index, 1)
                bearItDb.removeProject(id)
            }
        }
    },
    actions: {
        async loadList({ commit, dispatch }) {
            dispatch('startFetching', null, {root: true})
            commit('SET_CACHED', (await bearItDb.getProjectList()).map(addUiFlags));
            try {
                const serverList = (await api.userList()).map(addUiFlags).sort((a, b) => a.name.toLocaleLowerCase() < b.name.toLowerCase() ? -1 : 1)
                commit('SET_CACHED', serverList)
                commit('SET_SERVER', serverList)
            } catch (e) {}
            dispatch('stopFetching', null, {root: true})
        },
        async remove({ commit, dispatch }, projectId) {
            dispatch('startFetching', null, {root: true})
            commit('PREPARE_REMOVING', projectId)
            await api.remove(projectId)
            commit('REMOVE', projectId)
            dispatch('stopFetching', null, {root: true})
        }
    }
}
