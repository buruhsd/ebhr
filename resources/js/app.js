require('./bootstrap');

import Vue from 'vue'
import router from './router'
import store from './store'

// Set Vue globally
window.Vue = require('vue').default;
Vue.config.productionTip = false

Vue.component('index', require('./components/Index.vue').default);
const app = new Vue({
    el: '#app',
    router,
    store,
    created () {
        const userInfo = localStorage.getItem('user')
        if (userInfo) {
            const userData = JSON.parse(userInfo)
            this.$store.commit('setUserData', userData)
        }
        axios.interceptors.response.use(
          response => response,
          error => {
            if (error.response.status === 401) {
                this.$store.dispatch('logout')
            }
            return Promise.reject(error)
          }
        )
    },
});
