import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import offlineStorage from '~/lib/offlineStorage'

Vue.use(Vuex)

const appElement = document.getElementById('app')
const config = appElement
  ? JSON.parse(appElement.dataset.config || '{}')
  : {}

let offlineTimeout = null;

const storeOptions = {
  state: {
    user: null,
    config: config,
    fetching: false,
    offline: !navigator.onLine,
    connectAttempts: 0
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
    },
    SET_FETCHING_STATE(state, fetchingState) {
      state.fetching = fetchingState
    },
    OFFLINE(state) {
      state.offline = true
    },
    ONLINE(state) {
      state.offline = false
    },
    INC_CONNECT_ATTEMPTS(state) {
      state.connectAttempts++;
    },
    RESET_CONNECT_ATTEMPTS(state) {
      state.connectAttempts = 0;
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
    },
    checkOffline({ commit, dispatch, state }) {
      clearTimeout(offlineTimeout);
      fetch('/images/meOnlineWow.jpg', {cache: "no-store"}).then(async () => {
        commit('ONLINE');
        commit('RESET_CONNECT_ATTEMPTS');
        (await offlineStorage).startHandling();
      }).catch(async () => {
        (await offlineStorage).stopHandling();
        commit('OFFLINE');
        commit('INC_CONNECT_ATTEMPTS');
        offlineTimeout = setTimeout(() => {
          dispatch('checkOffline')
        }, Math.min(60000, 1000 * state.connectAttempts))
      })
    }
  }
}

const store = new Vuex.Store(storeOptions)

store.dispatch('checkOffline');

document.body.addEventListener("offline", function () {
  store.dispatch('checkOffline');
}, false);
document.body.addEventListener("online", function () {
  store.dispatch('checkOffline');
}, false);

export default store

export const loader = new Promise(resolve => {
  store.dispatch('login').then(() => {
    resolve()
  })
})
