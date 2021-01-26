import getters from './getters'
import mutations from './mutations'
import actions from './actions'

const state = {
  currentCurrency: 'USD',
  locales: [],
  currencies: [],
  themes: [],
  cities: [],
  facilities: [
    {
      type: 'INTERNET',
      icon: 'wifi'
    },
    {
      type: 'RESTAURANT',
      icon: 'fire'
    },
    {
      type: 'BAR',
      icon: 'fire'
    },
    {
      type: 'HANDICAP_ACCESSIBLE',
      icon: 'fire'
    },
    {
      type: 'MEETING_FACILITY',
      icon: 'fire'
    },
    {
      type: 'BREAKFAST',
      icon: 'coffee'
    },
    {
      type: 'ROOM_SERVICE',
      icon: 'fire'
    },
    {
      type: 'AIR_CONDITIONING',
      icon: 'fire'
    },
    {
      type: 'BANQUET_FACILITY',
      icon: 'fire'
    },
    {
      type: 'PARKING',
      icon: 'paypal'
    },
    {
      type: 'LIFT',
      icon: 'fire'
    },
    {
      type: 'KITCHEN',
      icon: 'fire'
    },
    {
      type: 'CHILD_ACTIVITY',
      icon: 'man'
    },
    {
      type: 'FITNESS_FACILITY',
      icon: 'fitness'
    },
    {
      type: 'MEETING_FACILITY',
      icon: 'people'
    },
    {
      type: 'BANQUET_FACILITY',
      icon: 'fire'
    },
    {
      type: 'CHILD_CARE',
      icon: 'bath'
    },
    {
      type: 'PETS_PERMITTED',
      icon: 'pet'
    }
  ]
}

export default {
  state,
  getters,
  mutations,
  actions
}
