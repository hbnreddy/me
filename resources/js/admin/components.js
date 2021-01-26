import Vue from 'vue'
import AdminApp from './AdminApp'
import Sidebar from './components/Sidebar'
import ToggleInput from './../components/ToggleInput'
import Paginate from 'vuejs-paginate'

Vue.component('paginate', Paginate)
Vue.component('admin-app', AdminApp)
Vue.component('sidebar', Sidebar)
Vue.component('toggle-input', ToggleInput)
