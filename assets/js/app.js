import '../styles/app.scss'
import '@babel/polyfill'

import './plugins/fontawesome'
import vuetify from './plugins/vuetify'

import Vue from 'vue'
import router from './router'
import store, { loader } from './store'

new Vue({
  vuetify,
  router,
  store,
  data() {
    return {
      loaded: false,
      drawer: null
    }
  },
  computed: {
    loggedIn() {
      return this.user !== null
    },
    user() {
      return this.$store.state.user
    },
    hasDrawer() {
      return (
        !this.$route.meta.hasOwnProperty('drawer') ||
        this.$route.meta.drawer === true
      )
    }
  },
  created() {
    loader.then(() => {
      this.loaded = true
    })
  }
}).$mount('#app')

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js');
}
