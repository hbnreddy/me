import getters from './getters'
import mutations from './mutations'
import actions from './actions'

import auth from './modules/auth'
import query from './modules/query'
import cart from './modules/cart'
import checkout from './modules/checkout'

const state = {
  themes: [],
  loading: false,
  city: false,
  place: false,
  notifications: {
    count: 0
  }
}

export default {
  modules: {
    auth,
    cart,
    checkout,
    query
  },
  state,
  getters,
  mutations,
  actions
}
