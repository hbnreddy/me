import getters from './getters'
import mutations from './mutations'
import actions from './actions'

const state = {
  currentItinerary: false,
  currentPlanId: false,
  plans: [],
  cart: {
    addedPlaces: {
    },
    plans: []
  },
  travellers: {
    adults: 0,
    children: 0,
    infants: 0
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
