require('./components')

import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import states from './store/store'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import Translator from '../translator'

window.Vue = require('vue')

Vue.use(Translator)
Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(Loading)

import {routes} from './routes'

const router = new VueRouter({
  routes
})

let store = new Vuex.Store({
  ...states
})

new Vue({
  el: '#vue-admin-app',
  router,
  store
})
