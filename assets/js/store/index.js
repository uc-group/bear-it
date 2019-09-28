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
    config: config
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
    }
  },
  actions: {
    async login({ commit }) {
      const response = await axios.get('/api/login')
      commit(
        'SET_USER',
        response.data.authenticated ? response.data.userData : null
      )
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
