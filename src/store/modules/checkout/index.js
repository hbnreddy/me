import getters from './getters'
import mutations from './mutations'
import actions from './actions/index'

const state = {
  plans: [],
  currentStep: 'planning',
  steps: {
    activities: {
      complete: false,
      errors: []
    },
    travel: {
      complete: false,
      errors: []
    },
    hotels: {
      complete: false,
      errors: []
    },
    travellers: {
      complete: false,
      errors: []
    }
  }
}

export default {
  state,
  getters,
  mutations,
  actions
}
