import getters from './getters'
import mutations from './mutations'
import actions from './actions'

const state = {
  checkoutStep: 1,
  travellers: []
}

export default {
  state,
  getters,
  mutations,
  actions
}
