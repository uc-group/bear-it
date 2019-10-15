export default {
    namespaced: true,
    state: {
      message: ''
    },
    mutations: {
        SET_MESSAGE(state, message ){
            state.message = message
        }
    },
    actions: {
        setMessage({ commit }, options) {
            if (options.type === 'success'){
                commit('SET_MESSAGE', options.message)
            }
            else if(options.type === 'error') {
                commit('SET_MESSAGE', options.message)
            }
        }
    },
    getters: {
        showMessage: state => !!state.message
    }
}