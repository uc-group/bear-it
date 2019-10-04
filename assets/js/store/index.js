import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

const appElement = document.getElementById('app')
const config = appElement
  ? JSON.parse(appElement.dataset.config || '{}')
  : {}

const storeOptions = {
  state: {
    user: null,
    config: config,
    fetching: false
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
    },
    SET_FETCHING_STATE(state, fetchingState) {
      state.fetching = fetchingState
    }
  },
  actions: {
    async login({ commit }) {
      const response = await axios.get('/api/login')
      commit(
        'SET_USER',
        response.data.authenticated ? response.data.userData : null
      )
    },
    startFetching({ commit }) {
      commit('SET_FETCHING_STATE', true)
    },
    stopFetching({ commit }) {
      commit('SET_FETCHING_STATE', false)
    }
  }
}

const store = new Vuex.Store(storeOptions)

export default store

export const loader = new Promise(resolve => {
  store.dispatch('login').then(() => {
    resolve()
  })
})
