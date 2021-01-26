import Vue from 'vue'
import Notification from '../../../components/notification'
import AppRouter from '../../../app-router'
import Translator from '../../../translator'

Vue.use(Translator)
Vue.use(AppRouter)
Vue.use(Notification)

require('./components/index')

window.Vue = require('vue')

new Vue({
  el: '#vue-app'
})
