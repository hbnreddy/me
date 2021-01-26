import getters from './getters'
import mutations from './mutations'
import actions from './actions'

const state = {
  loading: false,
  continents: {
    enabled: [],
    disabled: []
  },
  countries: {
    enabled: [],
    disabled: []
  },
  cities: {
    enabled: [],
    disabled: []
  },
  places: [],
  venues: [],
  themes: [],
  markers: [],
  place: null,
  user: null
}

export default {
  state,
  getters,
  mutations,
  actions
}
