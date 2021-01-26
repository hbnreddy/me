import Vue from 'vue'
import ConstInstaller from './../../constants'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import OnLoad from 'vue-onload'
import VueCarousel from 'vue-carousel'
import VueGallery from 'vue-gallery'
import moment from 'moment'
import AppRouter from '../../../app-router'
import Translator from '../../../translator'

Vue.use(Translator)
Vue.use(AppRouter)
Vue.prototype.moment = moment
Vue.use(VueCarousel)
Vue.use(OnLoad)
Vue.use(VueRouter)
Vue.use(VueGallery)
Vue.use(Vuex)
Vue.use(ConstInstaller)

const states = require('./../../store/store').default

const store = new Vuex.Store({
  ...states
})

window.vueApp = new Vue({
  el: '#vue-app',
  store
})
