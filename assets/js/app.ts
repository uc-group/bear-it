import '../styles/app.style'
import 'vuetify/dist/vuetify.min.css'
import '@babel/polyfill'

import Vue from 'vue'
import Vuetify from 'vuetify'
import router from './router'
import store, { loader } from './store'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'

Vue.component('fa-icon', FontAwesomeIcon)
library.add(fas)
library.add(fab)

Vue.use(Vuetify, {
  icons: {
    bearit: ''
  }
})

new Vue({
  router,
  store,
  el: '#app',
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
})

if ('serviceWorker' in navigator) {
  //navigator.serviceWorker.register('/sw.js');
}
