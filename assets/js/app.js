import '../styles/app.scss'
import '@babel/polyfill'
import '~/offlineHandlers'

import './plugins/fontawesome'
import vuetify from './plugins/vuetify'

import Vue from 'vue'
import router from './router'
import store, { loader } from './store'
import { mapState } from 'vuex'
import ProfileMenu from './layout/components/ProfileMenu'
import AlertList from './layout/components/AlertList'
import ProjectMenu from './layout/components/ProjectMenu'

new Vue({
  vuetify,
  router,
  store,
  components: {
    ProfileMenu,
    AlertList,
    ProjectMenu
  },
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
    hasDrawer() {
      if (this.loading) {
        return false;
      }
      if (!this.loggedIn) {
        return false;
      }
      return (
        !this.$route.meta.hasOwnProperty('drawer') ||
        this.$route.meta.drawer === true
      )
    },
    ...mapState(['user', 'fetching']),
    ...mapState(['user', 'fetching', 'offline'])
  },
  created() {
    loader.then(() => {
      this.loaded = true
    })
  }
}).$mount('#app')

// if ('serviceWorker' in navigator) {
//   navigator.serviceWorker.register('/sw.js');
// }
