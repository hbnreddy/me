import getters from './getters'
import mutations from './mutations'
import actions from './actions'

const state = {
  query: {
    currency: 'USD',
    language: 'en',
    originCity: {
      fullName: ''
    },
    themes: {
    },
    travellers: {
      adults: 0,
      children: 0,
      infants: 0
    },
    date: {
      type: 'exact',
      start: false,
      end: false,
      flexible_type: false,
      month: false,
      dateString: ''
    }
  },
  cities: []
}

export default {
  state,
  getters,
  mutations,
  actions
}
