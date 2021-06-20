import { v4 as uuid } from 'uuid'
export default {
    namespaced: true,
    state: {
      messages: []
    },
    mutations: {
        ADD_MESSAGE(state, message){
            state.messages.push(message)
        },
        CLEAR_MESSAGES(state) {
            state.messages = []
        },
        REMOVE_MESSAGE(state, messageId) {
            const index = state.messages.findIndex((message) => message.id === messageId)
            if (index >= 0) {
                state.messages.splice(index, 1)
            }
        }
    },
    actions: {
        addMessage({ commit }, { text, type, timeout }) {
            const messageTimeout = timeout || 2000
            const id = uuid()
            commit('ADD_MESSAGE', { text, type: type || 'info', timeout: messageTimeout, id })
            if (messageTimeout > 0) {
                setTimeout(() => {
                    commit('REMOVE_MESSAGE', id)
                }, messageTimeout)
            }
        },
        dismissMessage({ commit }, id) {
            commit('REMOVE_MESSAGE', id)
        },
        clearMessages({ commit }) {
            commit('CLEAR_MESSAGES')
        }
    }
}
