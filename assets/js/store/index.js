import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import alerts from './modules/alerts'
import offlineStorage from '~/lib/offlineStorage'
import offlineProjects from './modules/offlineProjects'

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
    async checkOffline({ commit, dispatch, state }) {
      clearTimeout(offlineTimeout);
      (await offlineStorage).stopHandling();
      fetch('/images/meOnlineWow.jpg', {cache: "no-store"}).then(async () => {
        commit('ONLINE');
        commit('RESET_CONNECT_ATTEMPTS');
        (await offlineStorage).startHandling();
      }).catch(async () => {
        commit('OFFLINE');
        commit('INC_CONNECT_ATTEMPTS');
        offlineTimeout = setTimeout(() => {
          dispatch('checkOffline')
        }, Math.min(60000, 1000 * state.connectAttempts))
      })
    }
  },
  modules: {
    offlineProjects,
    alerts,
  }
}

const store = new Vuex.Store(storeOptions);

(async function () {
  await store.dispatch('offlineProjects/init')
  store.dispatch('checkOffline');
})().then(() => {
  document.body.addEventListener("offline", function () {
    store.dispatch('checkOffline');
  }, false);
  document.body.addEventListener("online", function () {
    store.dispatch('checkOffline');
  }, false);
})

export default store

export const loader = new Promise(resolve => {
  store.dispatch('login').then(() => {
    resolve()
  })
})
