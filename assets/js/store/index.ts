import Vue from 'vue'
import Vuex, { StoreOptions, MutationTree } from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export interface Config {
  githubClientId: string
}

export interface UserAuth {
  id: string
  login: string
  avatar: string | null
  name: string
}

export interface RootState {
  user: UserAuth | null
  config: Config
}

const appElement = document.getElementById('app')
const config: Config = appElement
  ? JSON.parse(appElement.dataset.config || '{}')
  : {}

const storeOptions: StoreOptions<RootState> = {
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
