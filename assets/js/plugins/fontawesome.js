import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import Vue from 'vue'

Vue.component('fa-icon', FontAwesomeIcon)
library.add(fas)
library.add(fab)