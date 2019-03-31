import '../styles/app.style';
import 'vuetify/dist/vuetify.min.css';
import Vue from 'vue';
import Vuex from 'vuex';
import Vuetify from 'vuetify';
import VueRouter from 'vue-router';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import axios from 'axios';

Vue.component('fa-icon', FontAwesomeIcon);
library.add(fas);
library.add(fab);

Vue.use(Vuetify, {
    icons: {
        'bearit': ''
    }
});
Vue.use(VueRouter);
Vue.use(Vuex);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {name: 'dashboard', path: '/', component: () => import('./pages/dashboard.vue'), meta: {drawer: true}},
        {name: 'login', path: '/login', component: () => import('./pages/login.vue'), meta: {auth: false}}
    ]
});

const config = JSON.parse(document.getElementById('app').dataset.config)
const store = new Vuex.Store({
    state: {
        user: null,
        config: config
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user;
        }
    },
    actions: {
       login({ commit }) {
           return fetch('/api/login').then((response) => {
               return response.json();
           }).then((data) => {
               commit('SET_USER', data.authenticated ? data.userData : null)
           });
       }
    }
});

const loader = new Promise((resolve) => {
    store.dispatch('login').then(() => {
        resolve();
    })
});

router.beforeEach((to, from, next) => {
    loader.then(() => {
        if ((!to.meta.hasOwnProperty('auth') || to.meta.auth === true) && !store.state.user) {
            next('/login');
        } else if (to.name === 'login' && store.state.user) {
            next('/');
        }

        next();
    });
});

new Vue({
    router,
    store,
    el: '#app',
    data() {
        return {
            loaded: false
        }
    },
    computed: {
        loggedIn() {
            return this.user !== null;
        },
        user() {
            return this.$store.state.user;
        }
    },
    created() {
        loader.then(() => {
            this.loaded = true;
        })
    }
});

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}