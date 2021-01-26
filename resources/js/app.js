require('./components')

import VueRouter from 'vue-router'
import VueCarousel from 'vue-carousel'
import Vue from 'vue'
import Vuex from 'vuex'
import moment from 'moment'
import OnLoad from 'vue-onload'

window.Vue = require('vue')

Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VueCarousel)
Vue.use(OnLoad)

Vue.prototype.moment = moment

import {routes} from './routes'

const router = new VueRouter({
  mode: 'history',
  routes
})

import store from './frontend/store/store'

let vueStore = new Vuex.Store({
  ...store
})

new Vue({
  el: '#vue-app',
  router,
  store: vueStore
})
