import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import storage from './modules/storage'
import query from './modules/query'
import booking from './modules/booking'
import auth from './modules/auth'
import entity from './modules/entity'
import checkout from './modules/checkout'
import travel from './modules/travel'
import hotel from './modules/hotel'
import checks from './modules/checks'
import notifications from './modules/notifications'

import getters from './getters'
import mutations from './mutations'
import actions from './actions'

const state = {
    loading: false
}

export default new Vuex.Store({
    modules: {
        storage,
        auth,
        entity,
        booking,
        checkout,
        query,
        travel,
        hotel,
        checks,
        notifications
    },
    state,
    getters,
    mutations,
    actions
})