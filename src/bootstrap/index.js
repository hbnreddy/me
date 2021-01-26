require('./config')
require('./components')

import Vue from 'vue'
import moment from 'moment'
import VueCarousel from 'vue-carousel'
import ConstInstaller from './constants'
import Translator from './translator'
import VueSmoothScroll from 'vue2-smooth-scroll'
import VueGallery from 'vue-gallery'
import Notification from '../components/notification'
import VueLocalStorage from 'vue-localstorage'
import EventHub from 'vue-event-hub'
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../assets/sass/frontend/pages/welcome/index.scss'

Vue.use(EventHub)
Vue.use(VueLocalStorage)
Vue.use(VueCarousel)
Vue.use(Translator)
Vue.use(ConstInstaller)
Vue.use(VueSmoothScroll)
Vue.use(VueGallery)
Vue.use(Notification)

Vue.prototype.moment = moment

import axios from 'axios'

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*'
    /**
     * Vue app config
     */

Vue.config.silent = false
Vue.config.devtools = true

// Vue.config.errorHandler = function (err, vm, info) {
//   console.error('[ERROR]')
// }