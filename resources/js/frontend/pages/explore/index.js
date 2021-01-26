import Vue from 'vue'
import ConstInstaller from '../../constants'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import OnLoad from 'vue-onload'
import VueCarousel from 'vue-carousel'
import VueGallery from 'vue-gallery'
import moment from 'moment'
import Notification from '../../../components/notification'
import AppRouter from '../../../app-router'
import Translator from '../../../translator'

Vue.use(Translator)
Vue.use(AppRouter)
Vue.use(Notification)
Vue.prototype.moment = moment
Vue.use(VueCarousel)
Vue.use(OnLoad)
Vue.use(VueRouter)
Vue.use(VueGallery)
Vue.use(Vuex)
Vue.use(ConstInstaller)

import ExplorePage from './ExplorePage'
import LoadingCircle from '../../../components/LoadingCircle'

Vue.component('explore-page', ExplorePage)
Vue.component('loading-circle', LoadingCircle)

import {routes} from './routes'

const router = new VueRouter({
  routes
})

const states = require('./../../store/store').default

const store = new Vuex.Store({
  ...states
})

window.vueApp = new Vue({
  el: '#vue-app',
  mixins: [
    require('../../../translator')
  ],
  router,
  store
})
