import {cloneDeep} from 'lodash'

export default {
  setCurrentCurrency (state, payload) {
    state.currentCurrency = payload
  },

  setLocales (state, payload) {
    state.locales = cloneDeep(payload)
  },

  setThemes (state, payload) {
    state.themes = cloneDeep(payload)
  },

  setCities (state, payload) {
    state.cities = cloneDeep(payload)
  },

  setCurrencies (state, payload) {
    state.currencies = cloneDeep(payload)
  }
}
