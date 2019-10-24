import offlineStorage from '~/lib/offlineStorage'

let updateListTimeout = null;

export default {
    namespaced: true,
    state: {
        createdProjects: []
    },
    mutations: {
        ADD_CREATED_PROJECT(state, {id, name, description}) {
            state.createdProjects.push({
                id,
                name,
                description,
                sync: false,
                errors: false
            })
        },
        ADD_SYNC_ERROR_PROJECT(state, {id, name, description, errors}) {
            state.createdProjects.push({
                id,
                name,
                description,
                sync: false,
                errors
            })
        },
        REMOVE_PROJECT(state, id) {
            const index = state.createdProjects.findIndex(project => project.id === id)
            if (index >= 0) {
                state.createdProjects.splice(index, 1)
            }
        },
        RESET_STATE(state) {
            state.createdProjects = []
        },
        SYNC_PROJECT(state, {id, syncState}) {
            const project = state.createdProjects.find(project => project.id === id)
            if (project) {
                project.sync = syncState
            }
        }
    },
    actions: {
        async init({commit, dispatch}) {
            commit('RESET_STATE');
            const onPut = event => {
                if (event.name === 'project_create') {
                    commit('ADD_CREATED_PROJECT', event.payload)
                } else if (event.name === 'project_create_sync_error') {
                    commit('ADD_SYNC_ERROR_PROJECT', event.payload)
                }
            }

            const onRemove = event => {
                clearTimeout(updateListTimeout);
                if (['project_create', 'project_create_sync_error'].includes(event.name)) {
                    commit('REMOVE_PROJECT', event.payload.id)
                    updateListTimeout = setTimeout(() => {
                        dispatch('projectList/loadList', null, {root: true})
                    }, 1500)
                }
            }

            const storage = await offlineStorage;

            storage.addListener('put', onPut)
            storage.forEachEvent(onPut)
            storage.addListener('remove', onRemove)
            storage.addListener('beforeHandle', event => {
                if (event.name === 'project_create') {
                    commit('SYNC_PROJECT', {id: event.payload.id, syncState: true})
                }
            })
            storage.addListener('afterHandle', event => {
                if (event.name === 'project_create') {
                    commit('SYNC_PROJECT', {id: event.payload.id, syncState: false})
                }
            })
        }
    }
}
