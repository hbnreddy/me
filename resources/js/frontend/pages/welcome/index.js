import Vue from 'vue'
import ConstInstaller from '../../constants'
import Notification from '../../../components/notification'
import AppRouter from '../../../app-router'
import Translator from '../../../translator'
import VueSmoothScroll from 'vue2-smooth-scroll'

Vue.use(Translator)
Vue.use(AppRouter)
Vue.use(Notification)
Vue.use(ConstInstaller)
Vue.use(VueSmoothScroll)

import Welcome from './Welcome'
Vue.component('welcome', Welcome)

new Vue({
    el: '#vue-app'
})