require('./bootstrap');

import Vue from 'vue'
import router from './router'
import store from './store'
import App from './components/Index.vue'
import Spinner from 'vue-simple-spinner'
import VueSwal from 'vue-swal'

// Set Vue globally
window.Vue = require('vue').default;
Vue.config.productionTip = false
Vue.use(VueSwal)
Vue.use(Spinner)
Vue.component('vue-simple-spinner',require('vue-simple-spinner'))
Vue.component('vue-pagination', require('laravel-vue-pagination'))

new Vue({
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
    render: h => h(App)
}).$mount('#app');
