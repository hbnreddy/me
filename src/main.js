import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

require('./bootstrap')

const mixins = [
    require('./bootstrap/translator')
]

Vue.config.productionTip = false

new Vue({
    mixins,
    router,
    store,
    render: h => h(App)
}).$mount('#app')